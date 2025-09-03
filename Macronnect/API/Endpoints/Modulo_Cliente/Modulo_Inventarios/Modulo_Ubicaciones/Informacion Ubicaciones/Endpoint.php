<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/inventarios/ubicaciones/__ubicacionId__/");

    // Values Endpoint
    $dataEndpoint->setTitle("Detalle Ubicacion Articulo en Almacen");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_CLIENT);
    $dataEndpoint->setDescription("Este Endpoint Consulta el Detalle de una Ubicacion de un Articulo en el Almacen. La Busqueda es por el Id de la Ubicacion");
    $dataEndpoint->setNotes([
        "Si se quiere Limitar el Numero de Registros a obtener en la Llamada a la API se deben usar los parametros 'limit' (Numero que indica el limite de registros a mostrar) y 'offset' (Indica la posicion inicial desde la cual tomar los registros, por lo general es 0)"
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>