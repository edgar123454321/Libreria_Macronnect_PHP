<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/inventarios/pedidos/__PedidoId__/pdf");

    // Values Endpoint
    $dataEndpoint->setTitle("Pedidos Pendientes para Imprimir - Descarga de PDF -");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Descarga el Pdf de un Pedido pendiente por Imprimir. En automatico marca este pedido como impreso y ya no sale en la consulta del Endpoint 'Pendientes Impresion'.\n\nHabilita el parametro 'Tipo Respuesta' activando 'Descargar un Archivo' en caso de probar este Endpoint desde el Modulo Web.");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>