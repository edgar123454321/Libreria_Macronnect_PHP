<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("consulta/inventarios/articulos-pedidos");
    $dataEndpoint->setVersionAPI("");

    // Values Endpoint
    $dataEndpoint->setTitle("Detalle Pedidos");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setDescription("Este Endpoint Consulta El Detalle de los Pedidos");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [
        
        "Plantilla 1" => [
            "almacen-surte-id" => new NecessaryVariable("__INT_ID_ALMACEN_SURTE__","integer"), 
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"), 
            "estatus" => ["ACTIVO", "SURTIDO", "CANCELADO"], 
            "con-asignado" => false, 
            "tiposDocumento" => [2], 
            "almacen-id" => new NecessaryVariable("__INT_ID_ALMACEN_SOLICITA__","integer"), 
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string")
        ],

        "Otro" => []
    ];

    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);

    return $dataEndpoint;

?>