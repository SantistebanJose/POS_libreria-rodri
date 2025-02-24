<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorUsuario($accion);
}

function controladorUsuario($accion)
{
    switch ($accion) {
        case 'REGISTRARUSUARIO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            registrar_usuario($data);
            break;
        case 'EDITARRUSUARIO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            editar_usuario($data);
            break;
        case 'BLOQUEARUSUARIO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_usuario($id, $accion);
            break;
        case 'DESBLOQUEARUSUARIO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_usuario($id, $accion);
            break;
        case 'ELIMINARUSUARIO':
            $id = $_POST["id"]; // Decodificar JSON
            eliminar_usuario($id);
            break;
    }
}


function registrar_usuario($datos = array()) {
    global $conectar;

    try {
        // Verificar si persona_id ya está asociado con un usuario
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE persona_id = :persona_id");
        $verificarUsuario->bindParam(":persona_id", $datos['persona_id']);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente > 0) {
            // Si la persona_id ya está asociada con un usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Esta persona ya está asociado con un usuario."]);
            return;
        }

        // Insertar en la tabla usuario
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO usuario (username, password, rol, persona_id)
                                     VALUES (:username, :password, :rol, :persona_id);");
        $orden->bindParam(":username", $datos['username']);
        $hashedPassword = password_hash($datos['contraseña'], PASSWORD_BCRYPT);
        $orden->bindParam(":password", $hashedPassword);
        $orden->bindParam(":rol", $datos['rol']);
        $orden->bindParam(":persona_id", $datos['persona_id']);

        $orden->execute();
        $usuario_id = $conectar->lastInsertId(); // Obtener el ID del usuario recién insertado
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "usuario_id" => $usuario_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function editar_usuario($datos = array()) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE id = :id");
        $verificarUsuario->bindParam(":id", $datos['id']);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        
        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE usuario SET username = :username, rol = :rol";
        
        // Si la contraseña está presente, se actualizará
        if (!empty($datos['contraseña'])) {
            $hashedPassword = password_hash($datos['contraseña'], PASSWORD_BCRYPT);
            $sql .= ", password = :password";
        }
        
        $sql .= " WHERE id = :id";
        
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":username", $datos['username']);
        $orden->bindParam(":rol", $datos['rol']);
        $orden->bindParam(":id", $datos['id']);
        
        // Si se proporciona una nueva contraseña, se agrega
        if (!empty($datos['contraseña'])) {
            $orden->bindParam(":password", $hashedPassword);
        }

        $orden->execute();
        $conectar->commit();

        echo json_encode(["success" => true, "message" => "Usuario actualizado con éxito."]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_usuario($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEARUSUARIO") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE usuario SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEARUSUARIO") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE usuario SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del usuario actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function eliminar_usuario($id) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Verificar si el usuario está relacionado con la tabla compra o venta
        $verificarRelacionCompra = $conectar->prepare("SELECT COUNT(*) FROM compra WHERE usuario_id = :id");
        $verificarRelacionVenta = $conectar->prepare("SELECT COUNT(*) FROM venta WHERE usuario_id = :id");
        $verificarRelacionCompra->bindParam(":id", $id);
        $verificarRelacionVenta->bindParam(":id", $id);
        $verificarRelacionCompra->execute();
        $verificarRelacionVenta->execute();
        $relacionCompra = $verificarRelacionCompra->fetchColumn();
        $relacionVenta = $verificarRelacionVenta->fetchColumn();

        if ($relacionCompra > 0 || $relacionVenta > 0) {
            // Si hay relación, retornar un error
            echo json_encode(["error" => true, "message" => "El usuario no puede eliminarse porque está relacionado con compras o ventas."]);
            return;
        }

        // Eliminar al usuario si no hay relaciones
        $sql = "DELETE FROM usuario WHERE id = :id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Usuario eliminado con éxito."]);

    } catch (\Throwable $th) {
        error_log("Error en eliminar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
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
