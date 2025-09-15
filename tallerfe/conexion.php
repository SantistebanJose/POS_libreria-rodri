<?php

$manejador = "mysql";
$servidor = "localhost";
$usuario = "root"; // usuario con acceso a la base de datos, generalmente root
$pass = "";// aquí coloca la clave de la base de datos del servidor o hosting
$base = "isifacturacion"; //nombre de la base de datos
$cadena = "$manejador:host=$servidor;dbname=$base";

$cnx = new PDO($cadena, $usuario, $pass, array(PDO::ATTR_PERSISTENT => "true", PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));


//$sql="INSERT INTO unidad(id, descripcion) VALUES ('02','OTRA UNIDAD')";
$sql = "SELECT * FROM unidad";
$datos = $cnx->query($sql);

while($fila = $datos->fetch(PDO::FETCH_NAMED)){
	echo $fila['id']."-".$fila['descripcion'].'<br/>';
}


?>