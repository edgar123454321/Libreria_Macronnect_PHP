<?php

ini_set('memory_limit', '1024M');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");

define('BASE_PATH_API_MACRONNECT', __DIR__);

require_once(__DIR__ . "/../../GuzzleHttp/vendor/autoload.php");
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
require_once(__DIR__ . "/../ClassesServer/Communication/JSON.php");
require_once(__DIR__ . "/../ClassesServer/Maths/Number.php");
require_once(__DIR__ . "/../ClassesServer/GeneralError.php");

// Import Classes
require_once(__DIR__ . "/ConstantsMacronnect.php");
require_once(__DIR__ . "/Functions.php");
require_once(__DIR__ . "/DataConnection.php");
require_once(__DIR__ . "/VariableReplace.php");
require_once(__DIR__ . "/Parameter_GET.php");
require_once(__DIR__ . "/DataEndpoint.php");
require_once(__DIR__ . "/ResponseAPI.php");
require_once(__DIR__ . "/Statement.php");

// Import Connection Database
include_once(__DIR__ . "/../../Modelo/DataConnection_Sisga2.php");

class ApiMacronnect {

    // Import Endpoints
    private static array $filesImport = [
        "/Endpoints/Modulo_Servidor/Datos Expiracion Licencia/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Listar Clientes/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Creditos Autorizaciones/Saldos y Vencidos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Vendedores/Vendedores/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Rutas/Rutas/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Promociones/Paginado de Promociones/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Promociones/Consultar Promociones/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Ventas/Modulo_Promociones/Descuentos Aplicados en una Promocion/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Listar Articulos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Configuracion Almacenes/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Adicionales/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Articulos/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Codigos Adicionales/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Sustitutos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Articulos/Precio/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Listar Almacenes/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Ubicaciones/Informacion Ubicaciones/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Existencias/Consultar Existencia/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Precios/Precios por Moneda/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Busqueda Avanzada/Documentos Ventas/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Ventas/Documentos Ventas/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Ventas/Articulos Ventas/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Inventarios/Documentos Pedidos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Detalles/Pedidos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Inventarios/Documentos Traspasos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Inventarios/Detalle Pedidos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Consultas/Modulo_Inventarios/Modulo_Auxiliares/Auxiliar 1/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Costos/Proveedores/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Kardex/Articulos_Movimientos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Pedidos/Pendientes Impresion/Endpoint.php", 
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Pedidos/Pendientes Impresion Descarga Pdf/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Pedidos/Encabezado Pedido/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Unidades_Medida/Medida Unidades/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Inventarios/Modulo_Clasificaciones/Detalle Clasificaciones/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Compras/Modulo_Proveedores/Listar Proveedores/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Busqueda Avanzada/Articulos/Endpoint.php",
        "/Endpoints/Modulo_Cliente/Modulo_Generales/Modulo_Ivas/Detalle Iva/Endpoint.php",
        "/Endpoints/Modulo_Descargas/Descargas/Endpoint.php"
    ];


    // Mapping to Endpoints
    public static array $endpoints;
    public static function initEndpoints() {

        // Import Endpoints and Validate
        $endpoints_tmp = [];
        foreach(self::$filesImport as $fileImport) {
            $dataEndpoint = require_once BASE_PATH_API_MACRONNECT . $fileImport;

            // Validate no Duplicate in Key Endpoints
            DataEndpoint::validateUniqueness($endpoints_tmp, $dataEndpoint);

            // Validate a Method POST has Parameters Value
            if ($dataEndpoint->getMethod() == "POST" && is_null($dataEndpoint->getParametersSend_POST())) {
                throw new GeneralError(
                    "Error Parametros POST en Endpoint '" . $dataEndpoint->getEndpoint() . "'", 
                    "El Endpoint '" . $dataEndpoint->getEndpoint(). "' en el archivo '$fileImport' no contiene la Plantilla JSON para realizar la Peticion POST"
                );
            }

            // Query Folders Documentation Endpoint
            $pathFolder = dirname(BASE_PATH_API_MACRONNECT . $fileImport);
            $pathFolderDocumentation = $pathFolder . "/Documentacion";
            $arrayDocumentation = [
                "Path Folder" => $pathFolderDocumentation,
                "Folders Documentation" => []
            ];
            if (is_dir($pathFolderDocumentation)) {
                foreach(scandir($pathFolderDocumentation) as $item) {
                    if ($item === "."  || $item === "..") continue;
                    $arrayDocumentation["Folders Documentation"][] = $item;
                }
            }
            $dataEndpoint->setDocumentation($arrayDocumentation);

            // Add Endpoint
            $endpoints_tmp[] = $dataEndpoint;
        }
        self::$endpoints = $endpoints_tmp;
    }    

    public function __construct(array $dataUserConnection, string $nameDatabase) {
        $this->infoAuth = $dataUserConnection;
        $this->tenantId = $nameDatabase;
        $this->token = $this->getToken();
        $this->httpClient = new Client();
    }
    

    public function prepareRequest(string $endpoint, string $method): Statement {

        // Get Data Endpoint
        foreach(self::$endpoints as $dataEndpoint_i) {
            if ($dataEndpoint_i->getEndpoint() == $endpoint && $dataEndpoint_i->getMethod() == $method) {
                $statement = new Statement(clone $dataEndpoint_i, $this->tenantId, $this->token, $this->httpClient);
                return $statement;
            }
        }
        throw new GeneralError("Error Endpoint no Definido", "El Endpoint '$endpoint' de Tipo '$method' no esta definido en la clase ApiMacronnect");
    }    

    private function getToken(): string {
        
        $dsn = "mysql:host=" . DataConnection_Sisga2::$HOST . ";dbname=" . DataConnection_Sisga2::$BDNAME . ";charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, DataConnection_Sisga2::$USUARIO, DataConnection_Sisga2::$PASSWORD);
            // Enable Exceptions
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new GeneralError("Error de Conexion", $e->getMessage());
        }

        $sql = "SELECT value_field FROM Macronnect_Session WHERE name_field=? AND username=?";
        $stmt = $pdo->prepare($sql);    
        $stmt->execute([ "Token", ($this->infoAuth)["usuario"] ]);

        $response = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $response) {
           throw new GeneralError("Error Consultar Token", "No se pudo obtener el Token de Sesion");
        }
        $this->token = $response["value_field"];
        return $this->token;
    }


    // Function to Create New Token
    public function createNewToken(): string {
            
        // Prepare Endpoint
        $url = DataConnection::$URL_SERVER . ":" . DataConnection::$PORT_SERVER . "/login";

        $client = new Client();
        $jar = new CookieJar();
        try {
            $response = $client->post($url, [
                'json' => $this->infoAuth,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'cookies' => $jar,
                'http_errors' => false // To handle non-200 responses manually
            ]);

            // Check for successful response
            if ($response->getStatusCode() != 200) {
                $responseBody = $response->getBody();
                $responseData = json_decode($responseBody, true);
                throw new GeneralError("Error al Obtener Token Macronnect", "Respuesta Recibida: " . $responseBody);
            }

            // Retrieve cookies from the response
            foreach ($jar->toArray() as $cookie) {
                if ($cookie["Name"] === "SESSION") {
                    return "SESSION=" . $cookie["Value"];
                }
            }
            throw new GeneralError("Error al Obtener Token Macronnect", "No se encontró la Cookie de Sesión en la Respuesta Recibida");
        } catch (RequestException $e) {
            throw new GeneralError("Error al Obtener Token Macronnect", "Error al realizar la solicitud: " . $e->getMessage());
        }   
    }




}

ApiMacronnect::initEndpoints();


?>

