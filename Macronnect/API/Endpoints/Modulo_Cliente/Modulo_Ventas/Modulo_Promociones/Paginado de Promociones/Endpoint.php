<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/ventas/promociones/paginado");

    // Values Endpoint
    $dataEndpoint->setTitle("Paginado de Promociones");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta las Promociones por Paginado");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>