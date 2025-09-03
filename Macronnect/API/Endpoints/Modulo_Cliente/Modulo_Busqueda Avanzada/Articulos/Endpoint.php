<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/busqueda-avanzada/consulta/ARTICULOS");

    // Values Endpoint
    $dataEndpoint->setTitle("Busqueda Avanzada Listar Articulos");
    $dataEndpoint->setMethod("POST");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_APP_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta todos los Articulos como si fuera el modulo de Busqueda Avanzada");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);


    // Parameters to Send in POST (PLANTILLA GENERAL)
    // 
    // NOTA 1:
    // new stdClass() -> Significa esto -> {}
    $dataEndpoint_parametersPOST = [
        
        "Plantilla 1" => [
            "campos" => [
                ["codigo" => "clave", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "nombre", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "existencia", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "clasificacion.clave", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "precio-por-moneda", "filtros" => new stdClass(), "parametros" => ["clave-moneda-sat" => "MXN", "orden" => 1]],
                ["codigo" => "nombre-unidad-venta", "filtros" => new stdClass(), "parametros" => new stdClass()],
                ["codigo" => "ubicacion", "filtros" => new stdClass(), "parametros" => ["etiqueta" => "Ubicaci··n", "orden" => 7]],
                ["codigo" => "id", "filtros" => new stdClass(), "parametros" => new stdClass()]
            ],
            "parametros" => new stdClass(),
            "orden" => [
                ["codigo" => "nombre", "parametros" => new stdClass(), "direccion" => "asc"]
            ]
        ],

        "Otro" => []
    ];

    $dataEndpoint->setParametersSend_POST($dataEndpoint_parametersPOST);


    return $dataEndpoint;

?>