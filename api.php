<?php
// archivo: api/verificar_contraseña.php

header('Content-Type: application/json');
include 'logica/bd.php';  // Asumiendo que tienes un archivo de conexión a la base de datos (conexion.php)

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtener los datos de los parámetros de la URL
    if (isset($_GET['usuario_id']) && isset($_GET['contraseña'])) {
        $usuario_id = $_GET['usuario_id'];
        $contraseñaIngresada = $_GET['contraseña'];

        verificar_contraseña($usuario_id, $contraseñaIngresada);
    } else {
        echo json_encode(["error" => true, "message" => "Faltan parámetros"]);
    }
} else {
    echo json_encode(["error" => true, "message" => "Método de solicitud no permitido"]);
}

function verificar_contraseña($usuario_id, $contraseñaIngresada) {
    global $conectar;

    try {
        // Obtener el hash de la contraseña del usuario desde la base de datos
        $consulta = $conectar->prepare("SELECT password FROM usuario WHERE usuario_id = :usuario_id");
        $consulta->bindParam(":usuario_id", $usuario_id);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $hashedPassword = $resultado['password'];
            
            // Verificar si la contraseña ingresada coincide con el hash almacenado
            if (password_verify($contraseñaIngresada, $hashedPassword)) {
                // La contraseña es correcta
                echo json_encode(["success" => true, "message" => "Contraseña verificada correctamente"]);
            } else {
                // La contraseña es incorrecta
                echo json_encode(["error" => true, "message" => "Contraseña incorrecta"]);
            }
        } else {
            // Usuario no encontrado
            echo json_encode(["error" => true, "message" => "Usuario no encontrado"]);
        }

    } catch (\Throwable $th) {
        error_log("Error en verificar_contraseña: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}
?>

