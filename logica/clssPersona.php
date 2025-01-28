<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorVentaCorte($accion);
}

function controladorVentaCorte($accion)
{
    switch ($accion) {
        case 'CONSULTARRESERVA':
            $venta_id = $_POST["venta_id"];

            consultarDetalleReserva($venta_id);
            break;
        case 'REGISTRARPERSONARAPIDO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_persona($data); // Si tiene 'nombres' y 'apellidos', es persona
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                registrar_empresa($data); // Si tiene 'nombre_comercial' y 'razon_social', es empresa
            }
            break;
       
    }
}


function registrar_persona($datos = array()) {
    global $conectar;

    try {
        // Insertar en la tabla persona
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email);");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId(); // Obtener el ID de la persona recién insertada
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}


function registrar_empresa($datos = array()) {
    global $conectar;

    try {
        // Insertar en la tabla persona (aunque sea empresa, también se inserta en persona)
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombre_comercial, razon_social, telefonomovil, email)
                                     VALUES (:numero_documento, :nombre_comercial, :razon_social, :telefono_movil, :email);");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);
        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];

        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->execute();
        $empresa_id = $conectar->lastInsertId(); // Obtener el ID de la empresa recién insertada
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "empresa_id" => $empresa_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_empresa: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}