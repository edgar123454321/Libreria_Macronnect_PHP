<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/inventarios/pedidos/__idPedido__");

    // Values Endpoint
    $dataEndpoint->setTitle("Encabezado Pedido");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta el Encabezado de un Pedido por su Id");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>