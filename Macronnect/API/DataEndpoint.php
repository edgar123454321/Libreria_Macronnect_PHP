<?php 

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");

include_once __DIR__ . "./NecessaryVariable.php";

class DataEndpoint {

    // Variables
    private string $endpoint;
    private string $title;
    private string $method;
    private string $versionAPI;
    private string $description;
    private array $notes;
    private string $folderEndpoint;

    // Documentation
    private array $documentation;

    // Parameters Send POST
    private ?array $parametersSend_POST = null;


    public function __construct(string $endpoint) {
        $this->endpoint = $endpoint;
    }


    public static function validateUniqueness(array $arrayElements_DataEndpoints, $dataEndpoint): bool {
        $arrayTitles = [];
        $arrayEndpoints = [];
        foreach($arrayElements_DataEndpoints as $dataEndpoint_i) {

            // Valid Unique Title
            if ($dataEndpoint_i->getTitle() == $dataEndpoint->getTitle()) {
                $messageError = "En Endpoints existe un Título duplicado: '" . $dataEndpoint->getTitle() . "' en '" . $dataEndpoint->getFolderEndpoint(). "', ya existe definido en '" . $dataEndpoint_i->getFolderEndpoint() . "'";
                throw new Exception($messageError);
            }

            // Valid Unique Endpoint
            if ($dataEndpoint_i->getEndpoint() == $dataEndpoint->getEndpoint()) {
                $messageError = "En Endpoints existe un Endpoint duplicado: '" . $dataEndpoint->getEndpoint() . "' en '" . $dataEndpoint->getFolderEndpoint(). "', ya existe definido en '" . $dataEndpoint_i->getFolderEndpoint() . "'";
                throw new Exception($messageError);
            }

        }
        return true; 
    }

    private static function replaceNecessaryVariables(array $data): array {
        $response = [];
        foreach ($data as $key => $value) {
            // Si es un arreglo, procesar recursivamente
            if (is_array($value)) {
                $response[$key] = self::replaceNecessaryVariables($value);
            }
            // Si es instancia de NecessaryVariable, reemplazar por su nombre
            elseif ($value instanceof NecessaryVariable) {
                $response[$key] = $value->getNameVariable();
            }
            // En cualquier otro caso, conservar el valor
            else {
                $response[$key] = $value;
            }
        }
        return $response;
    }

    public static function extractNecessaryVariables($jsonTemplate) {
        $data = is_string($jsonTemplate) ? json_decode($jsonTemplate, true) : $jsonTemplate;
        $variables = [];

        $searchVariables = function($value) use (&$searchVariables, &$variables) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $searchVariables($item);
                }
            } elseif (is_object($value) && $value instanceof NecessaryVariable) {
                // Extraemos el nombre y tipo de la variable necesaria
                $varName = $value->getNameVariable();
                $varType = $value->getTypeVariable();
                $variables[$varName] = $varType;
            }
        };

        $searchVariables($data);
        return $variables;
    }
    
    public static function validateJsonReplacements($modifiedJson, $template, $necessaryVariables) {
        $modifiedData = is_string($modifiedJson) ? json_decode($modifiedJson, true) : $modifiedJson;
        $templateData = is_string($template) ? json_decode($template, true) : $template;
        $errors = [];

        $validateStructure = function($templatePart, $modifiedPart, $path = '') use (&$validateStructure, &$errors, $necessaryVariables) {
            foreach ($templatePart as $key => $templateValue) {
                $currentPath = $path ? "$path.$key" : $key;

                // Si el valor en el template es una instancia de NecessaryVariable
                if (is_object($templateValue) && $templateValue instanceof NecessaryVariable) {
                    $varName = $templateValue->getNameVariable();
                    $expectedType = $templateValue->getTypeVariable();
                    
                    // Verificar si existe en el JSON modificado
                    if (!array_key_exists($key, $modifiedPart)) {
                        $errors[] = "Falta reemplazar la variable '$varName' en '$currentPath'";
                        continue;
                    }

                    $replacedValue = $modifiedPart[$key];
                    
                    // PRIMERO: Verificar si el valor no fue reemplazado (contiene la marca original)
                    if (is_string($replacedValue) && $replacedValue === $varName) {
                        $errors[] = "Variable No Reemplazada: '$varName' en '$currentPath'";
                        continue;
                    }
                    
                    // SEGUNDO: Validar el tipo del valor reemplazado
                    $actualType = gettype($replacedValue);

                    // Ajustes para tipos equivalentes
                    if ($expectedType === 'float' && $actualType === 'double') {
                        $actualType = 'float';
                    }

                    // Validación de tipos
                    if ($expectedType === 'array' && !is_array($replacedValue)) {
                        $errors[] = "Tipo incorrecto para '$varName' en '$currentPath'. Esperado: array, Obtenido: $actualType";
                    }
                    elseif ($expectedType === 'bool' && $actualType !== 'boolean') {
                        $errors[] = "Tipo incorrecto para '$varName' en '$currentPath'. Esperado: boolean, Obtenido: $actualType";
                    }
                    elseif (!in_array($expectedType, ['array', 'bool']) && $actualType !== $expectedType) {
                        $errors[] = "Tipo incorrecto para '$varName' en '$currentPath'. Esperado: $expectedType, Obtenido: $actualType";
                    }
                }
                // Si es un array, validar recursivamente
                elseif (is_array($templateValue)) {
                    if (!isset($modifiedPart[$key]) || !is_array($modifiedPart[$key])) {
                        $errors[] = "Estructura incorrecta en '$currentPath'. Se esperaba un array";
                        continue;
                    }
                    $validateStructure($templateValue, $modifiedPart[$key], $currentPath);
                }
                // Si es un valor normal, verificar que coincida (opcional)
                elseif (isset($modifiedPart[$key]) && $modifiedPart[$key] !== $templateValue) {
                    // Opcional: Validar que los valores fijos no hayan cambiado
                    // $errors[] = "Valor fijo modificado en '$currentPath'";
                }
            }
        };

        $validateStructure($templateData, $modifiedData);

        return empty($errors) ? 
            ['valid' => true, 'message' => 'Todas las variables fueron reemplazadas correctamente'] : 
            ['valid' => false, 'errors' => $errors];
    }

    public static function normalizeJsonStructure($inputData, $template) {
        // Si ambos son arrays, procesar recursivamente
        if (is_array($inputData) && is_array($template)) {
            $result = [];
            
            foreach ($inputData as $key => $value) {
                // Verificar si la clave existe en el template
                if (array_key_exists($key, $template)) {
                    $result[$key] = self::normalizeJsonStructure($value, $template[$key]);
                } else {
                    $result[$key] = $value;
                }
            }
            
            return $result;
        }
        // Si el template es stdClass y el input es array vacío, convertirlo a stdClass
        elseif (is_array($inputData) && empty($inputData) && is_object($template) && $template instanceof stdClass) {
            return new stdClass();
        }
        // Si el template es NecessaryVariable, mantener el valor del input
        elseif (is_object($template) && $template instanceof NecessaryVariable) {
            return $inputData;
        }
        // Para cualquier otro caso, mantener el valor del input
        else {
            return $inputData;
        }
    }

    
    // Get Real Endpoint
    public function getRealEndpoint(): string {
        $realEndpoint = DataConnection::$URL_SERVER . ":" . DataConnection::$PORT_SERVER . "/" . $this->versionAPI. $this->endpoint;
        return $realEndpoint;
    }



    public function getJSON(): array {
        $json = [
            "Endpoint" => $this->getEndpoint(),
            "Title" => $this->getTitle(),
            "Method" => $this->getMethod(),
            "Version API" => $this->getVersionAPI(),
            "Description" => $this->getDescription(),
            "Notes" => $this->getNotes(),
            "Real Endpoint" => $this->getRealEndpoint(),
            "Documentation" => $this->getDocumentation()
        ];
        if ($this->getParametersSend_POST() != null) {
            $json["Parameters POST"] = self::replaceNecessaryVariables($this->getParametersSend_POST());
        }
        return $json;
    }  


    // GETTER AND SETTER
    public function getEndpoint(): string {
        return $this->endpoint;
    }
    public function setEndpoint(string $endpoint): void {
        $this->endpoint = $endpoint;
    }

    public function getTitle(): string {
        return $this->title;
    }
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getMethod(): string {
        return $this->method;
    }
    public function setMethod(string $method): void {
        $this->method = $method;
    }

    public function getVersionAPI(): string {
        return $this->versionAPI;
    }
    public function setVersionAPI(string $versionAPI): void {
        $this->versionAPI = $versionAPI;
    }

    public function getDescription(): string {
        return $this->description;
    }
    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getNotes(): array {
        return $this->notes;
    }
    public function setNotes(array $notes): void {
        $this->notes = $notes;
    }

    public function getFolderEndpoint(): string {
        return $this->folderEndpoint;
    }
    public function setFolderEndpoint(string $folderEndpoint): void {
        $this->folderEndpoint = $folderEndpoint;
    }

    public function getDocumentation(): array {
        return $this->documentation;
    }
    public function setDocumentation(array $documentation): void {
        $this->documentation = $documentation;
    }

    public function getParametersSend_POST(): ?array {
        return $this->parametersSend_POST;
    }
    public function setParametersSend_POST(array $parametersSend_POST): void {
        $this->parametersSend_POST = $parametersSend_POST;
    }

}


?>