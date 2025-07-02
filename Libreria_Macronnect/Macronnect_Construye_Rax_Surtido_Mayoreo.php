<?php 

ini_set('html_errors', 0);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");


include './Modelo/ModeloBD.php';
include './Controlador/Controlador.php';
$bd = new ModeloBD();
$c = new controlador($bd);


/// $beginDate = "2025-06-20T00:00:00";
// $endDate = "2025-06-20T00:00:00";
// $beginDate = date("Y-m-d") . "T00:00:00";
// $endDate = date("Y-m-d") . "T23:59:59";


$keyAlmacenes = [
    "002" => "CM",
    "003" => "BT",
    "004" => "LM",
    "005" => "ZA",
    "006" => "CF",
    "007" => "FO",
    "008" => "ZC",
    "009" => "MM",
    "010" => "ID",
    "011" => "GA",
    "015" => "AJ",
    "020" => "PR",
    "022" => "LR",
    "023" => "BM",
    "025" => "CH",
    "026" => "TL",
    "027" => "MG",
    "028" => "GY",
    "029" => "PU",
    "030" => "BR",
    "031" => "ML",
    "032" => "LP"
];


if (isset($_POST["Query_Elements_Insert_Surtido_Mayoreo"])) {
    try {
        
        $beginDate = $_POST["Begin_Date"];
        $endDate = $_POST["End_Date"];

        $recordSet_facturas = $c->API_Query_Documents_Surtido_Mayoreo($beginDate, $endDate);
        $response = [];

        foreach($recordSet_facturas as $dataFactura) {

            $recordsSurtidoFacturas = $c->Get_Records_Surtido_Mayoreo($dataFactura["Folio"]);

            if (count($recordsSurtidoFacturas) == 0) { // INSERT
            
                // Get Detail Factura
                $recordSet_articles = $c->API_Query_Articles_Folio_Surtido_Mayoreo($beginDate, $endDate, $dataFactura["Folio"]);

                foreach($recordSet_articles as $recordItemFactura) {
                    $response[] = [
                        "Estatus" => "Preparar para Insertar",
                        "Folio" => $dataFactura["Folio"],
                        "Begin_Date" => $beginDate,
                        "End_Date" => $endDate,
                        "Header Document" => $dataFactura,
                        "Body Document" => $recordItemFactura
                    ];
                }
            }
            else {
                if ($dataFactura["Estatus"] != "CANCELADO") continue;

                // Update Records to Cancelado
                if ($recordsSurtidoFacturas[0]["status_registro"] == 1) {
                    $c->Update_Status_Record_Surtido_Mayoreo($dataFactura["Folio"]);
                    $response[] = [
                        "Estatus" => "Factura Cancelada",
                        "Folio" => $dataFactura["Folio"]
                    ];
                }
            }
        }
        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


if (isset($_POST["Insert_Element_Detail_Surtido_Mayoreo"])) {
    try {

        $recordInsert = json_decode($_POST["Element_Insert"], true);
   
        $beginDate = $recordInsert["Begin_Date"];
        $endDate = $recordInsert["End_Date"];
        $dataFactura = $recordInsert["Header Document"];
        $recordItemFactura = $recordInsert["Body Document"];


        $almacen_info = explode("-", $recordItemFactura["Almacen"]);
        $almacenKey = trim($almacen_info[0]);

        // Get Id Almacen From Key
        $dataAlmacen = $c->API_Query_Data_Almacen_From_Key($almacenKey);
        $idAlmacen = $dataAlmacen["id"];

        // Get Id Article From Key
        $dataArticle = $c->API_Query_Data_Article_From_Key($recordItemFactura["Código"]);
        $idArticle = $dataArticle["id"];
        
        // Get Data Ubication
        $ubication1 = "";
        $ubication = $c->Macropro_Query_Ubication_by_Code_Article($recordItemFactura["Código"], "001");
        if (! empty($ubication)) {
            $ubication1 = $ubication["EXI_UBIC"];
        }
        
        // Get Ubication by Almacen
        /*$configAlmacenArticle = $c->API_Query_Config_Almacen_Article($idArticle, $idAlmacen);
        $ubications = $configAlmacenArticle["config_ubicaciones"];
        if (count($ubications) > 0) {
            $dataUbication1 = $ubications[0];
            $detailUbication = $c->API_Query_Detail_Ubication_by_Id_Ubication($dataUbication1["ubicacion_id"]);
            $ubication1 = $detailUbication["clave_completa"];
            if (is_null($ubication1)) {
                $ubication1 = "";
            }
        } else { // Query Ubication in MACROPRO
            $ubication = $c->Macropro_Query_Ubication_by_Code_Article($recordItemFactura["Código"], "001");
            if (! empty($ubication)) {
                $ubication1 = $ubication["EXI_UBIC"];
            }
        }*/

        // Get Linea Adicional Article
        $linea = "";
        $idAdicional = 13; // Adicional "LINEA"
        try {
            $dataAdicional = $c->API_Query_Adicional_Article($idArticle, $idAdicional);
            $linea = $dataAdicional["valor"];
        } catch (Error $e) {
            $linea = "";
        }

        // Query Existencias by Almacen
        $detailExistenciaArticle = $c->API_Query_Existencia_Article_in_Almacen($idArticle, $idAlmacen);
        $existenciaAlmacen = $detailExistenciaArticle["cantidad_existencia"];

        // Sugerencia Traspaso
        $sugerenciaTraspaso = "";
        $array_headersPedidos = $c->API_Query_Header_Pedidos_Almacen_Solicita($beginDate, $endDate, $idAlmacen);
        foreach($array_headersPedidos as $headerPedido) {
            $array_articlesPedidos = $c->API_Query_Articles_Pedido($headerPedido["ID"]);
            foreach($array_articlesPedidos as $bodyPedido) {
                if ($bodyPedido["articulo_id"] == $idArticle) {
                    $array_almacenSendArticle = explode("-", $headerPedido["Almacen solicitado"]);
                    $almacenSendArticle = trim($array_almacenSendArticle[0]);
                    if (! isset($keyAlmacenes[$almacenSendArticle])) {
                        throw new GeneralError("Error Almacen No Definido", "El Almacen '$almacenSendArticle' no tiene definida una clave");
                    }
                    $sugerenciaTraspaso .= $keyAlmacenes[$almacenSendArticle] . "[" . ($bodyPedido["cantidad"] * 1) ."] ";
                }
            }
        }

        // Get Values 
        $folio = $dataFactura["Folio"];
        $arrayCliente = explode("-", $dataFactura["Cliente"]);
        $cliente = trim($arrayCliente[1]);
        $quantity = $recordItemFactura["Unidades"];
        $description = $recordItemFactura["Descripcion"];
        $codeArticle = $recordItemFactura["Código"];
        $existenciaAlmacen = 'AC[' . ($existenciaAlmacen * 1) . ']';
        $array_date = explode("T", $dataFactura["Fecha"]);
        $horaFactura = $array_date[1];
        $horaActual = date('Y-m-d H:i:s', time());
        $statusSurtido = 0;
        $claveCliente = trim($arrayCliente[0]);

        // INSERT IN DB SISGA
        $c->Almacen_Insert_Surtido_Mayoreo($folio, $cliente, $quantity, $description, $codeArticle, $linea, $ubication1, $existenciaAlmacen, 
                    $sugerenciaTraspaso, $horaFactura, $horaActual, $statusSurtido, $claveCliente);

        $response = [
            "Estatus" => "Elemento Insertado",
            "Folio" => $folio, "Cliente" => $cliente, "Cantidad" => $quantity, "Description" => $description, "Codigo Articulo" => $codeArticle, 
            "Linea" => $linea, "Ubicacion" => $ubication1, "Existencia Almacen" => $existenciaAlmacen, "Sugerencia Traspaso" => $sugerenciaTraspaso, 
            "Hora Factura" => $horaFactura, "Hora Insert" => $horaActual, "Status Surtido" => $statusSurtido, "Clave Cliente" => $claveCliente
        ];

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


exit();

/*$recordSet_facturas = $c->API_Query_Documents_Surtido_Mayoreo($beginDate, $endDate);

$response = [];

foreach($recordSet_facturas as $dataFactura) {

    $recordsSurtidoFacturas = $c->Get_Records_Surtido_Mayoreo($dataFactura["Folio"]);

    if (count($recordsSurtidoFacturas) == 0) { // INSERT

        // Get Detail Factura
        $recordSet_articles = $c->API_Query_Articles_Folio_Surtido_Mayoreo($beginDate, $endDate, $dataFactura["Folio"]);

        foreach($recordSet_articles as $recordItemFactura) {

            $almacen_info = explode("-", $recordItemFactura["Almacen"]);
            $almacenKey = trim($almacen_info[0]);

            // Get Id Almacen From Key
            $dataAlmacen = $c->API_Query_Data_Almacen_From_Key($almacenKey);
            $idAlmacen = $dataAlmacen["id"];

            // Get Id Article From Key
            $dataArticle = $c->API_Query_Data_Article_From_Key($recordItemFactura["Código"]);
            $idArticle = $dataArticle["id"];

            // Get Ubication by Almacen
            $configAlmacenArticle = $c->API_Query_Config_Almacen_Article($idArticle, $idAlmacen);
            $ubications = $configAlmacenArticle["config_ubicaciones"];

            // Get Data Ubication
            $ubication1 = "";
            if (count($ubications) > 0) {
                $dataUbication1 = $ubications[0];
                $detailUbication = $c->API_Query_Detail_Ubication_by_Id_Ubication($dataUbication1["ubicacion_id"]);
                $ubication1 = $detailUbication["clave_completa"];
            }

            // Get Linea Adicional Article
            $idAdicional = 13; // Adicional "LINEA"
            $dataAdicional = $c->API_Query_Adicional_Article($idArticle, $idAdicional);
            $linea = $dataAdicional["valor"];

            // Query Existencias by Almacen
            $detailExistenciaArticle = $c->API_Query_Existencia_Article_in_Almacen($idArticle, $idAlmacen);
            $existenciaAlmacen = $detailExistenciaArticle["cantidad_existencia"];

            // Sugerencia Traspaso
            $sugerenciaTraspaso = "";
            $array_headersPedidos = $c->API_Query_Header_Pedidos_Almacen_Solicita($beginDate, $endDate, $idAlmacen);
            foreach($array_headersPedidos as $headerPedido) {
                $array_articlesPedidos = $c->API_Query_Articles_Pedido($headerPedido["ID"]);
                foreach($array_articlesPedidos as $bodyPedido) {
                    if ($bodyPedido["articulo_id"] == $idArticle) {
                        $array_almacenSendArticle = explode("-", $headerPedido["Almacen solicitado"]);
                        $almacenSendArticle = trim($array_almacenSendArticle[0]);
                        if (! isset($keyAlmacenes[$almacenSendArticle])) {
                            throw new GeneralError("Error Almacen No Definido", "El Almacen '$almacenSendArticle' no tiene definida una clave");
                        }
                        $sugerenciaTraspaso .= $keyAlmacenes[$almacenSendArticle] . "[" . ($bodyPedido["cantidad"] * 1) ."] ";
                    }
                }
            }

            // Get Values 
            $folio = $dataFactura["Folio"];
            $arrayCliente = explode("-", $dataFactura["Cliente"]);
            $cliente = trim($arrayCliente[1]);
            $quantity = $recordItemFactura["Unidades"];
            $description = $recordItemFactura["Descripcion"];
            $codeArticle = $recordItemFactura["Código"];
            $existenciaAlmacen = 'AC[' . ($existenciaAlmacen * 1) . ']';
            $array_date = explode("T", $dataFactura["Fecha"]);
            $horaFactura = $array_date[1];
            $horaActual = date('Y-m-d H:i:s', time());
            $statusSurtido = 0;
            $claveCliente = trim($arrayCliente[0]);

            // INSERT IN DB SISGA
            $c->Almacen_Insert_Surtido_Mayoreo($folio, $cliente, $quantity, $description, $codeArticle, $linea, $ubication1, $existenciaAlmacen, 
                        $sugerenciaTraspaso, $horaFactura, $horaActual, $statusSurtido, $claveCliente);

            $response[] = [
                "Estatus" => "Elemento Insertado",
                "Folio" => $folio, "Cliente" => $cliente, "Cantidad" => $quantity, "Description" => $description, "Codigo Articulo" => $codeArticle, 
                "Linea" => $linea, "Ubicacion" => $ubication1, "Existencia Almacen" => $existenciaAlmacen, "Sugerencia Traspaso" => $sugerenciaTraspaso, 
                "Hora Factura" => $horaFactura, "Hora Insert" => $horaActual, "Status Surtido" => $statusSurtido, "Clave Cliente" => $claveCliente
            ];
        }

    }
    else { // UPDATE

        if ($dataFactura["Estatus"] != "CANCELADO") continue;

        // Update Records to Cancelado
        if ($recordsSurtidoFacturas[0]["status_registro"] == 1) {
            $c->Update_Status_Record_Surtido_Mayoreo($dataFactura["Folio"]);
            $response[] = [
                "Estatus" => "Factura Cancelada",
                "Folio" => $dataFactura["Folio"]
            ];
        }
    }

}

$totalElements = count($response);
$acum = "Elementos Encontrados " . $totalElements;
if ($totalElements > 0) {
    for($i=0; $i<$totalElements; $i++) {
        $acum .= "<br>-> " . ($i+1) . ") ". json_encode($response[$i]);
    }
}

echo $acum;
*/

?>