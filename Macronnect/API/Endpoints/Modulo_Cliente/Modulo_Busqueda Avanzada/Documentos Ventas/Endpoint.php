<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/busqueda-avanzada/consulta/DOCUMENTOS_VENTAS");

    // Values Endpoint
    $dataEndpoint->setTitle("Consultar Documentos Ventas");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_APP_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint devuelve los Documentos de las Ventas Realizadas segun los Parametros de Busqueda");
    $dataEndpoint->setNotes([
        "Si se quiere Limitar el Numero de Registros a obtener en la Llamada a la API se deben usar los parametros 'limit' (Numero que indica el limite de registros a mostrar) y 'offset' (Indica la posicion inicial desde la cual tomar los registros, por lo general es 0)"
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [

        "Plantilla 1" => [
            "campos" => [
                ["codigo" => "folio", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "fecha", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "cliente.nombre", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "almacen.nombre", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "tipo-operacion.descripcion", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "costeo", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "moneda.clave-sat", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "total", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "estatus", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "comentarios", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "id", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "referenciados", "filtros" => new stdClass(), "parametros" => new stdClass()]
            ],
            "parametros" => [
                "fecha-inicial" => new NecessaryVariable("__STRING_FECHA_INICIAL__", "string"),
                "estatus" => new NecessaryVariable("__ARRAY_ESTATUS_DOCUMENTO__", "array"),
                "tipo-operacion" => new NecessaryVariable("__ARRAY_TIPO_OPERACION__", "array"),
                "almacenes" => new NecessaryVariable("__ARRAY_ALMACENES__", "array")
            ],
            "orden" => [
                ["codigo" => "folio", "parametros" => new stdClass(), "direccion" => "asc"]
            ]
        ],
        
        "Otro" => []
    ];
    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);


    // PARAMETERS SEND POST
    // -> EJEMPLO 1
    /* 
    $dataEndpoint_parametersPOST = [
        "campos" => [
            ["codigo" => "folio", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "fecha", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "cliente.nombre", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "almacen.nombre", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "tipo-operacion.descripcion", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "costeo", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "moneda.clave-sat", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "total", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "estatus", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "comentarios", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "id", "filtros" => new stdClass(), "parametros" => new stdClass()],
            ["codigo" => "referenciados", "filtros" => new stdClass(), "parametros" => new stdClass()]
        ],
        "parametros" => [
            "fecha-inicial" => "2025-06-04T00:00:00",
            "estatus" => ["ACTIVO"],
            "tipo-operacion" => [5],
            "almacenes" => [1]
        ],
        "orden" => [
            ["codigo" => "folio", "parametros" => new stdClass(), "direccion" => "asc"]
        ]
    ];
    */

    return $dataEndpoint;

?>