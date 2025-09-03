<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/inventarios/articulos/__articuloId__/adicionales");

    // Values Endpoint
    $dataEndpoint->setTitle("Adicionales del Articulo");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta los Adicionales de un Articulo por el Id del Articulo");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>