<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("consulta/inventarios/auxiliares/1");
    $dataEndpoint->setVersionAPI("");

    // Values Endpoint
    $dataEndpoint->setTitle("Auxiliar del Articulo");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setDescription("Este Endpoint Consulta el Auxiliar de un Articulo");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [
        
        "Plantilla 1" => [
            "incluir-cancelados" => false,
            "desdeArticulo" => new NecessaryVariable("__STRING_DESDE_ARTICULO__", "string"), 
            "hastaArticulo" => new NecessaryVariable("__STRING_HASTA_ARTICULO__", "string"),
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"),
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string")
        ],
        
        "Plantilla 2" => [
            "incluir-cancelados" => false,
            "desdeArticulo" => new NecessaryVariable("__STRING_DESDE_ARTICULO__", "string"), 
            "hastaArticulo" => new NecessaryVariable("__STRING_HASTA_ARTICULO__", "string"),
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"),
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string"),
            "almacenId" => new NecessaryVariable("__INTEGER_ALMACEN_ID__", "integer")
        ],

        "Otro" => []
    ];

    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);

    return $dataEndpoint;

?>