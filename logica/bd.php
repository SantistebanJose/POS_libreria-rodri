<?php
//para cuando lo subo a hosting
/**
$server = "localhost";
$bd = "id21952149_bd_comercial";
$user = "id21952149_admin";
$pass = "Franco2018@";

try {
    $conectar = new PDO("mysql:host=$server;dbname=$bd", $user, $pass);
} catch (\Throwable $th) {
    echo "" . $th->getMessage();
} 
 */

 $server = "localhost"; // O la IP del servidor PostgreSQL
 $bd = "bd_vysam";      // Nombre de la base de datos
 $user = "postgres";    // Usuario de PostgreSQL (por defecto es "postgres")
 $pass = "2018"; // Contraseña del usuario
 $port = "5432";        // Puerto de PostgreSQL (por defecto es 5432)

 try {
    // Incluye el puerto en el DSN
    $conectar = new PDO("pgsql:host=$server;port=$port;dbname=$bd", $user, $pass);
    $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Muestra el mensaje de error en caso de fallo
    echo "Error de conexión: " . $e->getMessage();
}