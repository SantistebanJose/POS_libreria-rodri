<?php
$server = "localhost";
$bd     = "sistema_libreria_rodri";
$user   = "postgres";
$pass   = "76008509";
$port   = "5432";

echo "<h3>Drivers PDO disponibles:</h3>";
echo implode(", ", PDO::getAvailableDrivers());
echo "<br><br>";

try {
    $con = new PDO("pgsql:host=$server;port=$port;dbname=$bd", $user, $pass);
    echo "✅ Conexión exitosa";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}