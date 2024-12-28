<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = $_ENV["HOST"];
$bd = $_ENV["DB_NAME"];
$user = $_ENV["DB_USER"];
$pass = $_ENV["PASSWORD"];

try {
    $conectar = new PDO("pgsql:host=$server;dbname=$bd", $user, $pass);
} catch (\Throwable $th) {
    echo "" . $th->getMessage();
}