<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("/licencia/expiracion");

    // Values Endpoint
    $dataEndpoint->setTitle("Expiracion Licencia Server");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI(DataConnection::$PATH_VERSION_API_SERVER);
    $dataEndpoint->setDescription("Obtiene La Fecha de Expiracion de la Licencia Macronnect");
    $dataEndpoint->setNotes([

    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>