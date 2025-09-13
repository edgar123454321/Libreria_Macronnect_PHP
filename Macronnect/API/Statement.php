<?php 
    include_once(__DIR__ . "/../../Modelo/DataConnection_Sisga2.php");
    require_once(__DIR__ . "/../../GuzzleHttp/vendor/autoload.php");
    use GuzzleHttp\Client;
    use GuzzleHttp\Cookie\CookieJar;
    use GuzzleHttp\Exception\RequestException;

    class Statement {

        private ?DataEndpoint $dataEndpoint = null;

        // Name Database
        private string $tenantId = "";

        // Token
        private ?string $token = null;

        // Parameters Request
        private array $parameters_GET = [];
        private array $parameters_POST = [];

        // Actual Template
        private string $actualTemplate_parameters_POST = "";

        // In case of Download a File
        private bool $typeResponse_File = false;
        private string $nameFileDownload = "";

        // Days Max to Save 
        private int $daysMaxSaveFiles = 2;
        

        public function __construct(DataEndpoint $dataEndpoint, string $tenantId, string $token) {
            $this->dataEndpoint = $dataEndpoint;
            $this->tenantId = $tenantId;
            $this->token = $token;
        }

        public function getToken(): string {
            if ($this->token != null) {
                return $this->token;
            }
            throw new GeneralError("Error Consultar Token", "El Token tiene un Valor NULL");
        }


        // Get Plantilla POST Endpoint
        public function getParameters_POST(string $nameTemplate): array {

            $arrayTemplates = $this->dataEndpoint->getParametersSend_POST();
            if (! isset($arrayTemplates[$nameTemplate])) {
                throw new GeneralError("Error Nombre Plantilla", "El nombre de la Plantilla '$nameTemplate' no se definio en el Endpoint '" . $this->dataEndpoint->getEndpoint() . "'");
            }

            $template = $arrayTemplates[$nameTemplate];
            return $template;
        }


        // Set Plantilla POST Endpoint
        public function setParameters_POST(string $nameTemplate, array $parameters_POST): void {
            $this->actualTemplate_parameters_POST = $nameTemplate;
            $this->parameters_POST = $parameters_POST;
        }
        

        // Replace Variables Required in Enpoint
        public function replaceVariables_inEndpoint(array $arrayParametersRequired_URL): void {

            $endpoint = $this->dataEndpoint->getEndpoint();
            foreach($arrayParametersRequired_URL as $variableReplace) {
                $endpoint = str_replace($variableReplace->getKeyReplace(), $variableReplace->getValueReplace(), $endpoint);
            }
            $this->dataEndpoint->setEndpoint($endpoint);
        }


        // Execute Request
        public function execute(): ResponseAPI {

            if (is_null($this->dataEndpoint)) {
                throw new GeneralError("Error Objeto Data Endpoint Null", "El Objeto Data Endpoint es Null");
            }

            if ($this->dataEndpoint->getMethod() == "GET") {
                return $this->consumeEndpoint_GET();
            }
            elseif ($this->dataEndpoint->getMethod() == "POST") {
                return $this->consumeEndpoint_POST();
            }
            else {
                throw new GeneralError("Error Tipo Metodo", "El Metodo " . $this->dataEndpoint->getMethod() . " no esta definido en Statement");
            }
        }


        // Consume Endpoint GET
        public function consumeEndpoint_GET(): ResponseAPI {
            
            // Get Token
            $this->getToken();

            // Prepare Endpoint
            $endpoint = $this->dataEndpoint->getRealEndpoint() . "?";
            
            // Process Parameters
            $parameters = $this->parameters_GET;
            for($i=0; $i < count($parameters); $i++) {
                $parameter_GET = $parameters[$i];
                $endpoint .= $parameter_GET->getKey() . ConstantsMacronnect::$TYPE_FILTERS[$parameter_GET->getOperator()] . $parameter_GET->getValue();
                if ($i != (count($parameters)-1)) {
                    $endpoint .= "&";
                } 
            }
            
            // Make Request
            $headers = [
                'tenantId' => $this->tenantId,
                'cookie' => $this->token
            ];
            $client = new Client();
            $jsonData = [];
            try {
                $response = $client->request('GET', $endpoint, [
                    'headers' => $headers
                ]);
                $body = $response->getBody();


                if ($this->typeResponse_File) { // Download a File
                    
                    // Clean Old Folders
                    Functions::cleanOldFolders(__DIR__ . "/Documents_Download/", $this->daysMaxSaveFiles);
                            
                    
                    $jsonData = $this->saveFile($body);
                }
                else { // Download JSON String

                    // Valid if is a File 
                    $contentType = $response->getHeaderLine('Content-Type');
                    if (preg_match('/application\/octet-stream|application\/pdf|image\/|zip|csv|excel|msword/', $contentType)) {
                        throw new GeneralError("Error Tipo de Respuesta Recibido", "Este Endpoint Regresa un Archivo. Prepara la Respuesta desde la instancia 'statement' o 'Tipo Respuesta' si estas probando el Endpoint desde el Modulo Web");
                    }

                    $jsonData = json_decode($body, true);
                    if (is_string($jsonData)) {
                        $jsonData = [$jsonData];
                    }
                    elseif(is_float($jsonData)) {
                        $jsonData = [$jsonData];
                    }
                }
                
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $body = $e->getResponse()->getBody()->getcontents();
                    $errorData = json_decode($body, true);
                    if (isset($errorData["mensaje"])) {
                        throw new GeneralError("Error al Consumir Endpoint", "Error en la Solicitud: " . $errorData["mensaje"]);
                    } else {
                        throw new Exception("Error al Consumir Endpoint", "Error en la Solicitud Mensaje Recibido: " . $body);
                    }
                }
                else {
                    throw new Exception("Error al Consumir Endpoint", "Error al Realizar la Solicitud: " . $e->getMessage());
                }
            }

            // Return Response API
            $responseAPI = new ResponseAPI("GET");
            $responseAPI->setActualToken($this->token);
            $responseAPI->setRequest($endpoint);
            $responseAPI->setResponseEndpoint($jsonData);

            return $responseAPI;
        }


        // Consume Endpoint POST
        public function consumeEndpoint_POST(): ResponseAPI {
        
            // Get Token
            $this->getToken();

            // Prepare Endpoint
            $endpoint = $this->dataEndpoint->getRealEndpoint() . "?";

            // Process Parameters
            $parameters = $this->parameters_GET;
            for($i=0; $i < count($parameters); $i++) {
                $parameter_GET = $parameters[$i];
                $endpoint .= $parameter_GET->getKey() . ConstantsMacronnect::$TYPE_FILTERS[$parameter_GET->getOperator()] . $parameter_GET->getValue();
                if ($i != (count($parameters)-1)) {
                    $endpoint .= "&";
                } 
            }

            // Validate parameters POST
            if ($this->actualTemplate_parameters_POST == "") {
                throw new GeneralError("Error Template", "Debes seleccionar algun Template de los Parametros a enviar en POST, con 'setParameters_POST(nameTemplate, arrayParameters)'");
            }
            elseif ($this->actualTemplate_parameters_POST == "Otro") {

                $parameters_POST_normalized = $this->parameters_POST;
            }
            else {
                $plantilla = $this->getParameters_POST($this->actualTemplate_parameters_POST);
                $mapNecessary = DataEndpoint::extractNecessaryVariables($plantilla);
                $response = DataEndpoint::validateJsonReplacements($this->parameters_POST, $plantilla, $mapNecessary);
                
                if (! $response["valid"]) {
                    throw new GeneralError("No se Ingresaron todos los Valores Necesarios a Reemplazar", implode("<br><br>", $response["errors"]));
                }

                // Normalized JSON 
                // For Example -> {} -> new stdClass() 
                // Because PHP convert -> {} -> [] (as an Empty array, and this is an error) 
                $parameters_POST_normalized = DataEndpoint::normalizeJsonStructure($this->parameters_POST, $plantilla);                
            }
            

            // Prepare Headers
            $headers = [
                'tenantId' => $this->tenantId,
                'cookie' => $this->token,
                'Content-Type' => 'application/json'
            ];

            // Client
            $client = new Client();
            $jsonData = [];

            /*return [
                "Actual Token" => $this->token,
                "Request" => $endpoint,
                "Body Sent" => json_encode($parameters_POST_normalized),
                "Response API" => []
            ];*/

            try {
                $response = $client->request('POST', $endpoint, [
                    'headers' => $headers,
                    'body' => json_encode($parameters_POST_normalized)
                ]);
                $body = $response->getBody();

                if ($this->typeResponse_File) { // Download a File
                    $jsonData = $this->saveFile($body);
                }
                else { // Download JSON String

                    // Valid if is a File 
                    $contentType = $response->getHeaderLine('Content-Type');
                    if (preg_match('/application\/octet-stream|application\/pdf|image\/|zip|csv|excel|msword/', $contentType)) {
                        throw new GeneralError("Error Tipo de Respuesta Recibido", "Este Endpoint Regresa un Archivo. Prepara la Respuesta desde la instancia 'statement' o 'Tipo Respuesta' si estas probando el Endpoint desde el Modulo Web");
                    }

                    $jsonData = json_decode($body, true);
                    if (is_string($jsonData)) {
                        $jsonData = [$jsonData];
                    }
                    if (is_float($jsonData)) {
                        $jsonData = [$jsonData];
                    }
                }

            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $body = $e->getResponse()->getBody()->getContents();
                    $errorData = json_decode($body, true);
                    if (isset($errorData["mensaje"])) {
                        throw new GeneralError("Error al Consumir Endpoint '$endpoint'", "Error en la Solicitud: " . $errorData["mensaje"]);
                    } else {
                        throw new GeneralError("Error al Consumir Endpoint '$endpoint'", "Error en la Solicitud no hay mensaje de respuesta: " . $body);
                    }
                } else {
                    throw new GeneralError("Error al Consumir Endpoint '$endpoint'", "Error al Realizar la Solicitud: " . $e->getMessage());
                }
            }

            // Return Response API
            $responseAPI = new ResponseAPI("POST");
            $responseAPI->setActualToken($this->token);
            $responseAPI->setRequest($endpoint);
            $responseAPI->setParametersSend_POST($parameters_POST_normalized);
            $responseAPI->setResponseEndpoint($jsonData);

            return $responseAPI;
        }


        public function getActualToken(): string {
            return $this->getToken();
        }

        // Function to Set Response to Download a File
        public function prepareResponseToDownload(string $nameFileDownload): void {

            // Valid Name File
            if (! (preg_match('/^[^\/:*?"<>|\\\]+?\.[a-zA-Z0-9]{1,5}$/', $nameFileDownload))) {
                throw new GeneralError("Error Nombre del Archivo a Descargar", "El Nombre del Archivo no es Valido, debe seguir el estandar 'archivo1.txt' por ejemplo");
            }

            $this->typeResponse_File = true; 
            $this->nameFileDownload = $nameFileDownload;
        }


        // Function to Save File 
        private function saveFile($body): array {
            $uuid = Functions::generate_uuid();
            $filename = $this->nameFileDownload;
            if (strstr($filename, '.')) {
                $filename = strstr($filename, '.', true); // Return all before point
            }
            $relativePath = "Documents_Download/$uuid" . "__" . date("Y_m_d_H_i_s") . "__" . $filename;
            $nameFolder = __DIR__ . "/" . $relativePath;
            mkdir($nameFolder);

            $pathFile = $nameFolder . "/" . $this->nameFileDownload;
            file_put_contents($pathFile, $body);

            $jsonData = [
                "Status" => "Success",
                "Path Complete File" => $pathFile,
                "Path Relative File" => "Macronnect/API/" . $relativePath . "/" . $this->nameFileDownload
            ];

            return $jsonData;
        }


        // GETTER AND SETTER
        public function getDataEndpoint(): ?DataEndpoint {
            return $this->dataEndpoint;
        }
        public function setDataEndpoint(DataEndpoint $dataEndpoint): void {
            $this->dataEndpoint = $dataEndpoint;
        }

        public function getParameters_GET(): array {
            return $this->parameters_GET;
        }
        public function setParameters_GET(array $parameters_GET): void {
            $this->parameters_GET = $parameters_GET;
        }

    }




?>