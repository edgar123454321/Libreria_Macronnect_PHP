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


$relationStatus = [
    "ACTIVO" => "T",
    "SURTIDO" => "S",
    "CANCELADO" => "C"
];

/**
 * This Function Query All Folios of Pedidos "Resurtidos"
 * From "(001) Almacen Central" to any other Sucursal
 */
if (isset($_POST["Query_Folios_Pedidos_Resurtido_Sucursales"])) {
    try {
        $beginDate = $_POST["Begin_Date"];
        $endDate = $_POST["End_Date"];

        $recordSet_foliosPedidos = $c->API_Query_Header_Pedidos_Resurtido_Sucursales($beginDate, $endDate);

        $response = [];
        foreach($recordSet_foliosPedidos as $headerPedido) {

            $actionPedido = [];

            $headerPedido_sisga = $c->Get_Header_Surtido_Sucursales($headerPedido["Folio"]);

            if (empty($headerPedido_sisga)) {
                $actionPedido["Status"] = "Insert in Sisga";
                $actionPedido["Header Pedido"] = $headerPedido;
                
                // INSERT
                $array_almacenSolicita = explode("-", $headerPedido["Almacen emisor"]);
                $keyAlmacen = $array_almacenSolicita[0];
                $response_insert = $c->Insert_Header_Surtido_Sucursales($headerPedido["Folio"], $headerPedido["Fecha"], $keyAlmacen);
                $actionPedido["Status Insert"] = $response_insert;

                $response[] = $actionPedido;
            }
            elseif ($headerPedido_sisga["status_surtido"] != $relationStatus[$headerPedido["Estatus"]]) {
                $actionPedido["Status"] = "Update Status in Sisga";
                $actionPedido["Header Pedido"] = $headerPedido;

                if ($headerPedido["Estatus"] == "SURTIDO") {

                    // GET FOLIO TRASPASO
                    $detailTraspaso = null;
                    $array_headersTraspasos = $c->API_Query_Header_Traspasos_Almacen_Solicitado(1, $beginDate, $endDate);
                    for ($i=0; $i < count($array_headersTraspasos); $i++) {
                        $headerTraspaso = $array_headersTraspasos[$i];
                        if ($headerTraspaso["Folio Pedidos"] == $headerPedido["Folio"]) {
                            $detailTraspaso = $headerTraspaso;
                            break;
                        }
                    }

                    // UPDATE FOLIO TRASPASO
                    if (is_null($detailTraspaso)) {
                        $actionPedido["Status Update"] = false;
                        $actionPedido["Message Status"] = "Sobre este Pedido '" . $headerPedido["Folio"] . "' SURTIDO no se encontro el Folio del Traspaso Aplicado";
                    }
                    else {
                        $c->Almacen_Update_Relacion_Surtido_Traspaso("S", $detailTraspaso["Fecha"], $detailTraspaso["Folio"], $headerPedido["Folio"]);
                        $actionPedido["Status Update"] = true;
                    }
                }
                elseif ($headerPedido["Estatus"] == "CANCELADO") {
                    $c->Almacen_Update_Relacion_Surtido_Traspaso("C", null, null, $headerPedido["Folio"]);
                    $actionPedido["Status Update"] = true;
                }

                $response[] = $actionPedido;
            }
        }   

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}



if (isset($_POST["Query_Detail_Pedido_Resurtido_Sucursales"])) {
    try {

        $idPedidoResurtido = $_POST["Id_Pedido_Resurtido"];
        $articles_pedido = $c->API_Query_Articles_Pedido($idPedidoResurtido);

        exit(JSON::returnSuccess($articles_pedido));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


if (isset($_POST["Insert_Article_Pedido_Resurtido"])) {
    try {
        
        $folioSurtido = $_POST["Folio_Surtido"];
        $articleId = $_POST["Article_Id"];
        $cantidadSurtir = $_POST["Cantidad_Surtir"];
        $almacenSolicita_key = $_POST["Almacen_Solicita"];
        $almacenSolicitado_key = $_POST["Almacen_Solicitado"];

        // Query Detail Article
        $detailArticle = $c->API_Query_Detail_Article_by_Id_Article($articleId);

        // Query Id Almacen Solicita
        $dataAlmacen_solicita = $c->API_Query_Data_Almacen_From_Key($almacenSolicita_key);
        $idAlmacen_solicita = $dataAlmacen_solicita["id"];

        // Query Id Almacen Solicitado 
        $dataAlmacen_solicitado = $c->API_Query_Data_Almacen_From_Key($almacenSolicitado_key);
        $idAlmacen_solicitado = $dataAlmacen_solicitado["id"];

        // Query Config Almacen of Article By "Almacen Solicita"
        $configAlmacenArticle_solicita = $c->API_Query_Config_Almacen_Article($articleId, $idAlmacen_solicita);

        // Query Config Almacen of Article by "Almacen Solicitado"
        // $configAlmacenArticle_solicitado = $c->API_Query_Config_Almacen_Article($articleId, $idAlmacen_solicitado);

        // Query Existencias "Almacen Solicita" 
        $dataExistencia_solicita_article = $c->API_Query_Existencia_Article_in_Almacen($articleId, $idAlmacen_solicita);

        // Query Existencias "Almacen Solicitado"
        $dataExistencia_solicitado_article = $c->API_Query_Existencia_Article_in_Almacen($articleId, $idAlmacen_solicitado);

        // Get Data Ubication in "Almacen Solicitado"
        $ubication_almacenSolicitado = "";
        $ubication = $c->Macropro_Query_Ubication_by_Code_Article($detailArticle["clave"], "001");
        if (! empty($ubication)) {
            $ubication_almacenSolicitado = $ubication["EXI_UBIC"];
        }
        
        /*$ubications = $configAlmacenArticle_solicitado["config_ubicaciones"];
        if (count($ubications) > 0) {
            $dataUbication1 = $ubications[0];
            $detailUbication = $c->API_Query_Detail_Ubication_by_Id_Ubication($dataUbication1["ubicacion_id"]);
            $ubication_almacenSolicitado = $detailUbication["clave_completa"];
            if (is_null($ubication_almacenSolicitado)) {
                $ubication_almacenSolicitado = "";
            }
        }
        else { // Query Ubication in MACROPRO
            $ubication = $c->Macropro_Query_Ubication_by_Code_Article($detailArticle["clave"], "001");
            if (! empty($ubication)) {
                $ubication_almacenSolicitado = $ubication["EXI_UBIC"];
            }
        }*/

        // Get Linea Adicional Article
        $linea = "";
        $idAdicional = 13; // Adicional "LINEA"
        try {
            $dataAdicional = $c->API_Query_Adicional_Article($articleId, $idAdicional);
            $linea = $dataAdicional["valor"];
        } catch (Error $e) {
            $linea = "";
        }

        // Tipo Surtido | Pues la Verdad nunca supe pa que es, pero pues asi estan todos los registros actuales
        $tipoSurtido = 1;

        $response = [
            "folio_surtido" => $folioSurtido,
            "codigo" => $detailArticle["clave"],
            "descripcion" => $detailArticle["nombre"],
            "abc" => $detailArticle["abc"],
            "max_suc" => $configAlmacenArticle_solicita["maximo"],
            "reo_suc" => $configAlmacenArticle_solicita["reorden"],
            "exi_suc" => $dataExistencia_solicita_article["cantidad_existencia"],
            "surtir" => $cantidadSurtir,
            "exi_alm" => $dataExistencia_solicitado_article["cantidad_existencia"],
            "max_alm" => $configAlmacenArticle_solicitado["maximo"],
            "ubi_alm" => $ubication_almacenSolicitado,
            "linea" => $linea,
            "tipo_surtido" => $tipoSurtido,
            "bujia_surtido" => ''
        ];

        // Insert in DB SISGA
        $responseInsert = $c->Insert_Surtido_Detalle_Articles($response["folio_surtido"], $response["codigo"], $response["descripcion"], $response["abc"], 
                            $response["max_suc"], $response["reo_suc"], $response["exi_suc"], $response["surtir"], $response["exi_alm"], $response["max_alm"], 
                            $response["ubi_alm"], $response["linea"], $response["tipo_surtido"], $response["bujia_surtido"]);

        $response["Status Insert"] = $responseInsert;

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}



?>