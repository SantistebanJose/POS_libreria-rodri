<?php
//para cuando lo subo a hosting

$server = "localhost"; // O la IP del servidor PostgreSQL
$bd = "sistema_libreria_rodri";      // Nombre de la base de datos
$user = "postgres";    // Usuario de PostgreSQL (por defecto es "postgres")
$pass = "76008509"; // Contraseña del usuario
$port = "5432";        // Puerto de PostgreSQL (por defecto es 5432)

try {
    // Incluye el puerto en el DSN
    $conectar = new PDO("pgsql:host=$server;port=$port;dbname=$bd", $user, $pass);
    $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    // Muestra el mensaje de error en caso de fallo
    echo "Error de conexión: " . $e->getMessage();
}


//