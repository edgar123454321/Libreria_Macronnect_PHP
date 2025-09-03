<?php 

    require_once BASE_PATH_API_MACRONNECT . "/DataConnection.php";
    require_once BASE_PATH_API_MACRONNECT . "/DataEndpoint.php";
    require_once BASE_PATH_API_MACRONNECT . "/NecessaryVariable.php";

    // URL Endpoint
    $dataEndpoint = new DataEndpoint("descargas/__idDocumento__/");

    // Values Endpoint
    $dataEndpoint->setTitle("Descarga Documento Generado por Reporte");
    $dataEndpoint->setMethod("GET");
    $dataEndpoint->setVersionAPI("");
    $dataEndpoint->setDescription("");
    $dataEndpoint->setNotes([
        
    ]);
    $dataEndpoint->setFolderEndpoint(__DIR__);

    return $dataEndpoint;

?>