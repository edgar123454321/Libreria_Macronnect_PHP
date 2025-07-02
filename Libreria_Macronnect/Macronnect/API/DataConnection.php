<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");

class DataConnection {
    
    // Data Connection Server
    public static string $URL_SERVER = "74.208.42.111";
    public static int $PORT_SERVER = 8080;

    // Common Base Url Endpoint
    public static string $PATH_VERSION_API_CLIENT = "api/v1";
    public static string $PATH_VERSION_API_SERVER = "api/server";
    public static string $PATH_VERSION_APP_CLIENT = "app/v1";

    // Name Data Base
    // public static string $TENANT_ID = "demo"; // Pruebas
    public static string $TENANT_ID = "dinamica";

    // Data User Connection
    public static array $DATA_USER_CONEXION_API = [
        "usuario" => "CONEXIONAPI",
        "contrasena" => "Pegaso.315",
        "uuid" => "4C4C4544-0046-4E10-8036-C7C04F395433",
        "tipo_sesion" => "ESCRITORIO",
        "direccion_mac" => "04-BF-1B-22-13-47",
        "reemplazar" => 1
    ];


}


?>