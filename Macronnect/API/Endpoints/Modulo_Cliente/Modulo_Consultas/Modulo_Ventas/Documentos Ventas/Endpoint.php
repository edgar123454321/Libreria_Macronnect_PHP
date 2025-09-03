<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("consulta/ventas/documentos");

    // Values Endpoint
    $dataEndpoint->setTitle("Reporte Consulta Documentos de Ventas");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setVersionAPI("");
    $dataEndpoint->setDescription("Este Endpoint proveniente del Modulode de Ventas Reportes devuelve los Documentos de las Ventas Realizadas segun los Parametros de Busqueda");
    $dataEndpoint->setNotes([
        //"Si se quiere Limitar el Numero de Registros a obtener en la Llamada a la API se deben usar los parametros 'limit' (Numero que indica el limite de registros a mostrar) y 'offset' (Indica la posicion inicial desde la cual tomar los registros, por lo general es 0)"
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [
        "Plantilla 1" => [
            "monedasIds" => [1,2],
            "desdeFecha" => new NecessaryVariable("__STRING_FECHA_INICIO__", "string"),
            "tiposOperacionIds" => new NecessaryVariable("__ARRAY_TIPOS_OPERACIONES_IDS__", "array"),
            "estatus" => ["ACTIVO","CANCELADO"],
            "almacenId" => new NecessaryVariable("__INT_ALMACEN_ID__", "integer"),
            "tiposDocumentoIds" => [],
            "hastaFecha" => new NecessaryVariable("__STRING_FECHA_FIN__", "string"),
            "comentario" => ""
        ], 
        "Otro" => []
    ];

    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);

    return $dataEndpoint;

?>