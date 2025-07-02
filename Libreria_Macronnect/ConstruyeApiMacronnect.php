<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");

include_once(__DIR__ . "./Modelo/DataConnection_Sisga.php");
require_once("./Macronnect/API/ApiMacronnect.php");
require_once("./Macronnect/ClassesServer/Communication/JSON.php");
require_once("./Macronnect/ClassesServer/GeneralError.php");


$apiMacronnect = new ApiMacronnect(DataConnection::$DATA_USER_CONEXION_API, DataConnection::$TENANT_ID);

set_error_handler(["JSON", "handleUnexpectedError"]); // From import JSON.php Class


if (isset($_POST["QueryEndpoint"])) {
    
    $endpoint = $_POST["Endpoint"];
    $method = $_POST["Method"];

    $statement = $apiMacronnect->prepareRequest($endpoint, $method);
    $dataEndpoint = $statement->getDataEndpoint();
    $dataEndpoint_json = $dataEndpoint->getJSON();

    exit(JSON::returnSuccess($dataEndpoint_json));
}

if (isset($_POST["ConsumeApiMacronnect"])) {
    try {

        // Get Data
        $endpoint = $_POST["Endpoint"];
        $method = $_POST["Method"];
        $listParameters_GET = json_decode($_POST["parameters_URL"], true);

        // Get Data Endpoint
        $statement = $apiMacronnect->prepareRequest($endpoint, $method);
        $endpointData = $statement->getDataEndpoint();

        // Replace Parameters Required URL, if exist
        if (isset($_POST["Parameters_Required_URL"])) {
            $arrayParametersRequired_URL = [];
            $parametersRequired = json_decode($_POST["Parameters_Required_URL"], true);
            foreach($parametersRequired as $parameterRequired) {
                $arrayParametersRequired_URL[] = new VariableReplace($parameterRequired["Key_Replace"], $parameterRequired["Value_Replace"]);
            }
            $statement->replaceVariables_inEndpoint($arrayParametersRequired_URL);
        }

        $arrayElements_GET = [];
        foreach($listParameters_GET as $element) {
            $arrayElements_GET[] = new Parameter_GET($element["Key"], $element["Operator"], $element["Value"]);
        }
        $statement->setParameters_GET($arrayElements_GET);

        if ($method == "POST") {
            // Data JSON Send Endpoint
            if (! isset($_POST["JSON_Send"])) {
                throw new GeneralError("Error JSON Enviar POST", "No Existe un JSON el cual enviar en la Peticion POST");
            }
            $parameters_POST = json_decode($_POST["JSON_Send"], true);
            $nameTemplate = $_POST["Name_Template"];
            $statement->setParameters_POST($nameTemplate, $parameters_POST);
        }

        $responseAPI = $statement->execute();
        $response = $responseAPI->getAsArray();

        exit(JSON::returnSuccess($response));
        
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor",$e->getMessage()));
    }
}


if (isset($_POST["GetImagesDocumentation"])) {
    try {
        $endpoint = $_POST["Endpoint"];
        $method = $_POST["Method"];
        $nameFolderDocumentation = $_POST["Name_Folder_Documentation"];

        $statement = $apiMacronnect->prepareRequest($endpoint, $method);
        $dataEndpoint = $statement->getDataEndpoint();

        $documentation = $dataEndpoint->getDocumentation();
        $pathFolderDocumentation = $documentation["Path Folder"] . "/" . $nameFolderDocumentation;
        
        // Obtener solo archivos (ignorando carpetas y . / ..)
        $archivos = array_filter(scandir($pathFolderDocumentation), function($archivo) use ($pathFolderDocumentation) {
            return is_file("$pathFolderDocumentation/$archivo") && $archivo !== '.' && $archivo !== '..';
        });

        // Ordenar por número inicial, y si no hay número, va al final
        usort($archivos, function($a, $b) {
            preg_match('/^(\d+)_/', $a, $matchA);
            preg_match('/^(\d+)_/', $b, $matchB);

            $numA = isset($matchA[1]) ? (int)$matchA[1] : PHP_INT_MAX;
            $numB = isset($matchB[1]) ? (int)$matchB[1] : PHP_INT_MAX;

            return $numA <=> $numB;
        });

        // Full Path Images
        foreach($archivos as &$archivo) {
            $archivo = $pathFolderDocumentation . "/" . $archivo;

            // Normalizar los separadores a '/'
            $normalized = str_replace('\\', '/', $archivo);

            // Separar en partes
            $parts = explode('/', $normalized);

            // Omitir los primeros 3 elementos
            $archivo = implode('/', array_slice($parts, 3));
        }
        unset($archivo);

        $dataResponse["Path Images"] = $archivos;

        exit(JSON::returnSuccess($dataResponse));        
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor",$e->getMessage()));
    }
}




?>