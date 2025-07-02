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

$almacenes_locales = ['001','002','003','004','005','006','007','008','009','010','011','012','020','022','023','027','028', '032'];
$almacenes_foraneos = ['015','025','026'];


if (isset($_POST["Query_All_Almacenes"])) {
    try {

        $listAlmacenes = $c->API_Query_List_All_Almacenes();
        $response = $listAlmacenes;

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


if (isset($_POST["Query_Articles_by_Page"])) {
    try {

        $sizeBatch = $_POST["Size_Batch"];
        $numberPage = $_POST["Number_Page"];

        $data_batch = $c->API_Query_List_Articles_by_Batch($sizeBatch, $numberPage);

        $response = $data_batch;

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


if (isset($_POST["Query_Articles_Last_Movements"])) {
    try {

        $dateBegin = $_POST["Date_Begin"];

        $listIdsArticles = $c->API_Query_All_Articles_Last_Movements($dateBegin);

        $response = $listIdsArticles;

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}



if (isset($_POST["Detail_Article_Update_Insert_Sisga"])) {
    try {
        
        $listArticles_NO = ['*','**','***','****','*****','+','-',''];
        $listLineas_NO = ['BAJA','DESA','GEODESIGN','HEBO','KAWASAKI','MOTOCARRO','MRM','MUNCIE','SIGN-X','SPEED','}SPED','TVS','XMP','BACK','PROMO','HCFT','HERRA','HUM','PLOMO','PROMOLUK'];

        $listAlmacenes = json_decode($_POST["List_Almacenes"], true);

        if (isset($_POST["Code_Article"])) {
            $codeArticle = $_POST["Code_Article"];

            // Query Id Article
            $dataArticle = $c->API_Query_Data_Article_From_Key($codeArticle);
            $idArticle = $dataArticle["id"];
        }
        elseif (isset($_POST["Id_Article"])) {
            $idArticle = $_POST["Id_Article"];

            // Query Code Article
            $dataArticle = $c->API_Query_Data_Article_From_Id_Article($idArticle);
            $codeArticle = $dataArticle["clave"];
        }


        // Valid Code Article
        if (in_array($codeArticle, $listArticles_NO)) {
            exit(JSON::returnSuccess(["Status" => "No Actualizar este Articulo '$codeArticle'"]));
        }
        

        // Query Adicionales Article
        $listAdicionales = $c->API_Query_All_Adicionales_Article($idArticle);
        $linea = "";
        $marca = "";
        $idAdicional_linea = 13; // Adicional "LINEA"
        $idAdicional_marca = 7; // Adicional "MARCA"
        foreach($listAdicionales as $detailAdicional) {

            if ($detailAdicional["adicional_id"] == $idAdicional_marca) { // Adicional "MARCA"
                $marca = $detailAdicional["valor"];
            }
            elseif ($detailAdicional["adicional_id"] == $idAdicional_linea) { // Adicional "LINEA"
                $linea = $detailAdicional["valor"];
                if (in_array($linea, $listLineas_NO)) {
                    exit(JSON::returnSuccess(["Status" => "No Actualizar el Articulo '$codeArticle' porque esta en la Linea: $linea"]));
                }
            }
            

        }

        // Query Classification Article
        $detailClassification = $c->API_Query_Detail_Classification($dataArticle["clasificacion_id"]);
        $classification = str_replace(" ", "", $detailClassification["clave_completa"]); // Replace Spaces

        // Query Existencias Article
        $article_existenciaLocal = 0;
        $article_existenciaForanea = 0;
        $recordSet_existencias = $c->API_Query_All_Existencias_by_Id_Article($idArticle);
        
        foreach($recordSet_existencias as $dataAlmacenExistencias) {

            // Query Data Almacen
            $idAlmacenExistencia_i = (string) $dataAlmacenExistencias["almacen_id"];
            $keyAlmacen = $listAlmacenes[$idAlmacenExistencia_i];
            
            if (in_array($keyAlmacen, $almacenes_locales)) {
                $article_existenciaLocal = $article_existenciaLocal + $dataAlmacenExistencias["cantidad_existencia"]; 
            }
            elseif (in_array($keyAlmacen, $almacenes_foraneos)) {
                $article_existenciaForanea = $article_existenciaForanea + $dataAlmacenExistencias["cantidad_existencia"];
            }
        }

        $article_existencia = $article_existenciaLocal + $article_existenciaForanea;

        // Query ABC
        $abc = $dataArticle["abc"];

        // Costo Article
        $costoArticle = $dataArticle["ultimo_costo_mxn"];

        // Multiplo Venta Article
        $parametroArticle = $dataArticle["parametro_articulo"];
        $multiplosVenta = $parametroArticle["art_par_multiplos_venta"];
        $multiplosVenta = (int) ($multiplosVenta);
        if ($multiplosVenta == 0) {
            $multiplosVenta = 1;
        }

        $detailArticle = [
            "Status" => "",
            "codigo_articulo" => $codeArticle,
            "linea_articulo" => $linea,
            "marca_articulo" => $marca,
            "clasificacion_articulo" => $classification,
            "existencia_articulo" => $article_existencia,
            "exi_local_articulo" => $article_existenciaLocal,
            "exi_foranea_articulo" => $article_existenciaForanea,
            "abc_articulo" => $abc,
            "costo_articulo" => $costoArticle,
            "multiplo_vta_articulo" => $multiplosVenta,
            "actualizacion_articulo" => date('Y-m-d H:i:s', time())
        ];


        // Article
        $exists = $c->Exists_Article_in_Sisga($codeArticle);
        if ($exists) { // UPDATE
            $responseUpdate = $c->Update_Article_Admin_MMLArticulos($detailArticle["codigo_articulo"], $detailArticle["linea_articulo"], $detailArticle["marca_articulo"], 
                $detailArticle["clasificacion_articulo"], $detailArticle["existencia_articulo"], $detailArticle["exi_local_articulo"], $detailArticle["exi_foranea_articulo"], 
                $detailArticle["abc_articulo"], $detailArticle["costo_articulo"], $detailArticle["multiplo_vta_articulo"], $detailArticle["actualizacion_articulo"]);
            if ($responseUpdate) {
                $detailArticle["Status"] = "UPDATE SUCCESS";
            }
            else {
                $detailArticle["Status"] = "UPDATE ERROR";
            }
        }
        else { // INSERT
            $responseInsert = $c->Insert_Article_Admin_MMLArticulos($detailArticle["codigo_articulo"], $detailArticle["linea_articulo"], $detailArticle["marca_articulo"], 
                $detailArticle["clasificacion_articulo"], $detailArticle["existencia_articulo"], $detailArticle["exi_local_articulo"], $detailArticle["exi_foranea_articulo"], 
                $detailArticle["abc_articulo"], $detailArticle["costo_articulo"], $detailArticle["multiplo_vta_articulo"], $detailArticle["actualizacion_articulo"]);
            if ($responseInsert) {
                $detailArticle["Status"] = "INSERT SUCCESS";
            }
            else {
                $detailArticle["Status"] = "INSERT ERROR";
            }
        }

        exit(JSON::returnSuccess($detailArticle));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}



?>