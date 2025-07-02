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

// Esto se hace asi porque ya hay modulos antes de la Migracion que tenian lo siguiente
// ctl_cli == 1 -> "ABIERTO"
// ctl_cli == 2 -> "LIMITADO"
// ctl_cli == 3 -> "AVISAR"
// ctl_cli == 4 -> "REVISAR"
// ELSE -> "CERRADO"

$typesControlSaldo = [
    "ABIERTO" => 1,
    "LIMITADO" => 2,
    "CERRADO" => 5
];

if (isset($_POST["Query_List_Clients_Mayoreo"])) {
    try {

        $listClientsMayoreo = $c->Query_All_Clients_Mayoreo();

        $response = $listClientsMayoreo;

        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


if (isset($_POST["Detail_Client_Update_Insert_Sisga"])) {
    try {
        $idClient = $_POST["Id_Client"];
        $claveClient = $_POST["Clave_Client"];

        $dataClient = $c->API_Query_Client_Mayoreo_by_Id_Client($idClient);

        // Query Saldo Cliente
        $detailSaldo = $c->API_Query_Saldos_y_Vencidos_MXN_by_Id_Client($idClient);
        $saldoClient = $detailSaldo["saldo"];

        // Query Limit Credit
        $limitCredit = $detailSaldo["credito"];

        // Control de Saldo Client
        $controlSaldo = $dataClient["control_saldo"];
        $idSisgaControlSaldo = $typesControlSaldo[$controlSaldo];

        // Ruta Clientes
        $idRutaClient = "";
        if (isset($dataClient["ruta_id"])) {
            $idRuta = $dataClient["ruta_id"];
            $detailRuta = $c->API_Query_Detail_Ruta_by_Id_Ruta($idRuta);
            $idRutaClient = trim($detailRuta["clave"]);
            if (strlen($idRutaClient) > 0) {
                $idRutaClient = str_pad($idRutaClient, 3, "0", STR_PAD_LEFT); // Completar con hasta 3 ceros a la izquierda
            }
        }

        // Vendedor Cliente
        $vendedorClient = "";
        if (isset($dataClient["vendedor_id"])) {
            $idVendedor = $dataClient["vendedor_id"];
            $detailVendedor = $c->API_Query_Detail_Vendedor_by_Id_Vendedor($idVendedor);
            $vendedorClient = trim($detailVendedor["clave"]);
            if (strlen($vendedorClient) > 0) {
                $vendedorClient = str_pad($vendedorClient, 4, "0", STR_PAD_LEFT); // Completar con hasta 3 ceros a la izquierda
            }
        }
        
        // Curp Client
        $curp = "";
        if (isset($_POST["curp"])) {
            $curp = $_POST["curp"];
        }

        $detailClient = [
            "Status" => "",
            "id_cli" => $claveClient,
            "nombre_cli" => $dataClient["nombre"],
            "rfc_cli" => $dataClient["rfc"],
            "curp" => $curp,
            "tipo_cli" => $dataClient["tipo_cliente_id"],
            "plazo_cli" => $dataClient["dias_credito"],
            "saldo_cli" => $saldoClient,
            "limite_cli" => $limitCredit,
            "ctl_cli" => $idSisgaControlSaldo,
            "pp_cli" => "",
            "fch_alta_cli" => $dataClient["fecha_creacion"],
            "fch_mod_cli" => $dataClient["fecha_ultima_actualizacion"],
            "obsv_cli" => "",
            "ruta_cli" => $idRutaClient,
            "vend_cli" => $vendedorClient,
            "kilometraje" => null,
            "tiempoTraslado" => null
        ];


        $exists = $c->Exists_Client_in_Admin_MMLClientes($claveClient);
        if ($exists) { // UPDATE
            $c->Update_Client_in_Admin_MMLClientes($detailClient["id_cli"], $detailClient["nombre_cli"], $detailClient["rfc_cli"], $detailClient["curp"], 
                $detailClient["tipo_cli"], $detailClient["plazo_cli"], $detailClient["saldo_cli"], $detailClient["limite_cli"], $detailClient["ctl_cli"], 
                $detailClient["pp_cli"], $detailClient["fch_alta_cli"], $detailClient["fch_mod_cli"], $detailClient["obsv_cli"], $detailClient["ruta_cli"], 
                $detailClient["vend_cli"], $detailClient["kilometraje"], $detailClient["tiempoTraslado"]);
            $detailClient["Status"] = "UPDATE SUCCESS";
        }
        else { // INSERT 
            $responseInsert = $c->Insert_Client_in_Admin_MMLClientes($detailClient["id_cli"], $detailClient["nombre_cli"], $detailClient["rfc_cli"], $detailClient["curp"], 
                $detailClient["tipo_cli"], $detailClient["plazo_cli"], $detailClient["saldo_cli"], $detailClient["limite_cli"], $detailClient["ctl_cli"], 
                $detailClient["pp_cli"], $detailClient["fch_alta_cli"], $detailClient["fch_mod_cli"], $detailClient["obsv_cli"], $detailClient["ruta_cli"], 
                $detailClient["vend_cli"], $detailClient["kilometraje"], $detailClient["tiempoTraslado"]);
            if ($responseInsert) {
                $detailClient["Status"] = "INSERT SUCCESS";
            }
            else {
                $detailClient["Status"] = "INSERT ERROR";
            }
        }

        $response = $detailClient;
        
        exit(JSON::returnSuccess($response));
    } catch (GeneralError $e) {
        exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
    } catch (Exception $e) {
        exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
    }
}


?>