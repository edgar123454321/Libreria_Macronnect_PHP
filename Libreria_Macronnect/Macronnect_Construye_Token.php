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

    if (isset($_POST["Create_New_Token"])) {
        $newToken = $c->API_Create_New_Token();
        $response = $c->Update_Token_Macronnect_in_Sisga($newToken, date("Y-m-d H:i:s"));

        if (! $response) {
            throw new GeneralError("Error Actualizar Token", "Ocurrio un Error al Actualizar el Token");
        }

        echo json_encode([
            "Status" => "Success",
            "Token" => $newToken
        ]);
    }

    if (isset($_POST["Validate_Token"])) {
        try {

            // Get Adicionales Article
            $idAdicional = 13; // Adicional "LINEA"
            $idArticle = 2561; // Code Article -> '1406013'
            $dataAdicional = $c->API_Query_Adicional_Article($idArticle, $idAdicional);

            $response = [
                "Status" => "Success"
            ];
            exit(JSON::returnSuccess($response));
        } catch (GeneralError $e) {
            exit(JSON::returnError($e->getTypeError(), $e->getMessageError()));
        } catch (Exception $e) {
            exit(JSON::returnError("Error Inesperado del Servidor", $e->getMessage()));
        }
    }

    




?>