<?php
//para cuando lo subo a hosting

// $server = "localhost"; // O la IP del servidor PostgreSQL
// $bd = "sistema_libreria_rodri";      // Nombre de la base de datos
// $user = "postgres";    // Usuario de PostgreSQL (por defecto es "postgres")
// $pass = "76008509"; // Contraseña del usuario
// $port = "5432";        // Puerto de PostgreSQL (por defecto es 5432)

$server = "aws-1-us-west-2.pooler.supabase.com"; // Host del Session pooler de Supabase
$bd = "postgres";      // Nombre de la base de datos
$user = "postgres.ubtlxfiumbewhsmpakcv";    // Usuario de Supabase (project ref incluido)
$pass = "uhZLk9rafYFVuh6G"; // Contraseña de la base de datos en Supabase
$port = "5432";        // Puerto del Session pooler

try {
    // Incluye el puerto en el DSN, más sslmode que exige Supabase
    $conectar = new PDO("pgsql:host=$server;port=$port;dbname=$bd;sslmode=require", $user, $pass);
    $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conectar->exec("SET timezone = 'America/Lima'"); // Corrige el desfase de hora (Perú UTC-5)

} catch (PDOException $e) {
    // Muestra el mensaje de error en caso de fallo
    echo "Error de conexión: " . $e->getMessage();
}


//