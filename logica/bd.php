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

$server = "localhost";
$bd = "peppers";
$user = "root";
$pass = "";

try {
    $conectar = new PDO("mysql:host=$server;dbname=$bd", $user, $pass);
    
} catch (\Throwable $th) {
    echo "" . $th->getMessage();
}
