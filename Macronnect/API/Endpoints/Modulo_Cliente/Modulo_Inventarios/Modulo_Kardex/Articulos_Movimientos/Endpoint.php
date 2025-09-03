<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/inventarios/kardex/articulos-con-movimientos");

    // Values Endpoint
    $dataEndpoint->setTitle("Consulta Articulos con Movimientos");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta los ids de los Articulos con Movimientos");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>