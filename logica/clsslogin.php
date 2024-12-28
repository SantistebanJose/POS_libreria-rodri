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
        $orden = $conectar ->prepare("select u.id,u.username,u.rol, p.nombres,p.email from usuario as u inner join persona as p on u.persona_id = p.id where u.deleted_at IS null AND username=:user AND password=:pass;");
        $orden->bindParam(":user", $user);
        $orden->bindParam(":pass", $pass);
        $orden->execute();

        $lista = $orden->fetch(PDO::FETCH_ASSOC);

        if($lista){
            session_start();
            $_SESSION['id'] = $lista["id"];
            $_SESSION['usuario'] = $lista["username"];
            $_SESSION['rol'] = $lista["rol"];
            $_SESSION['nombre'] = $lista["nombres"];
            $_SESSION['correo'] = $lista["email"];

            echo json_encode($lista);
        } else {
            echo json_encode(["error" => "Credenciales inválidas"]);
        }
    } catch (\Throwable $th) {
        echo json_encode(["error" => $th->getMessage()]);
    }
}

?>
