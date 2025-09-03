<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("consulta/inventarios/pedidos");
    $dataEndpoint->setVersionAPI("");

    // Values Endpoint
    $dataEndpoint->setTitle("Documentos Pedidos");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setDescription("Este Endpoint Consulta Los Documentos de Pedidos");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [
        
        "Plantilla 1" => [
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"),
            "estatus" => ["ACTIVO"], 
            "tiposDocumento" => [1],
            "almacenUnoId" => new NecessaryVariable("__INT_ID_ALMACEN_EMISOR__","integer"),
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string")
        ],

        "Plantilla 2" => [
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"),
            "estatus" => ["ACTIVO", "CANCELADO", "SURTIDO"],
            "almacenDosId" => new NecessaryVariable("__INT_ID_ALMACEN_EMISOR__","integer"),
            "tiposDocumento" => [2],
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string")
        ],

        "Otro" => []
    ];

    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);

    return $dataEndpoint;

?>