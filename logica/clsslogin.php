<?php
include("bd.php");

if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
    controladorLogin($accion);
}

function controladorLogin($accion){
    switch($accion){
        case 'LOGIN':
            $user = $_POST["user"];
            $pass = $_POST["password"];
            login($user, $pass);
            break;
        case 'CAMBIARCONTRASEÑA':
            break;
        case 'VALIDAR':

    }
}

function login($user, $pass){
    global $conectar;

    try{
        // Prepara la consulta para obtener los datos del usuario
        $orden = $conectar->prepare("SELECT u.id, u.username, u.rol, p.nombres, p.apellidos, p.email, u.password 
                                     FROM usuario AS u 
                                     INNER JOIN persona AS p ON u.persona_id = p.id 
                                     WHERE u.deleted_at IS NULL AND u.username = :user;");
        $orden->bindParam(":user", $user);
        $orden->execute();

        // Obtén los resultados
        $lista = $orden->fetch(PDO::FETCH_ASSOC);

        // Verifica si el usuario existe
        if ($lista) {
            // Verifica si la contraseña ingresada coincide con la almacenada (usando password_verify)
            if (password_verify($pass, $lista["password"])) {
                // Inicia sesión y guarda los datos en la sesión
                session_start();
                $_SESSION['id'] = $lista["id"];
                $_SESSION['usuario'] = $lista["username"];
                $_SESSION['rol'] = $lista["rol"];
                $_SESSION['nombre'] = $lista["nombres"];
                $_SESSION['ape'] = $lista["apellidos"];
                $_SESSION['correo'] = $lista["email"];

                echo json_encode($lista);  // Retorna los datos del usuario en formato JSON
            } else {
                echo json_encode(["error" => "Credenciales inválidas"]);  // Contraseña incorrecta
            }
        } else {
            echo json_encode(["error" => "Usuario no encontrado"]);  // Usuario no encontrado
        }
    } catch (\Throwable $th) {
        echo json_encode(["error" => $th->getMessage()]);  // Captura cualquier error y lo devuelve
    }
}


?>
