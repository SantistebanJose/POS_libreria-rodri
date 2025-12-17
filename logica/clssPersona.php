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
        case 'OBTENERPERSONA':
            $id = $_POST["id"]; 
            consultarPersona($id );
            break;

        case 'REGISTRARPERSONARAPIDO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_persona_rapido($data); // Si tiene 'nombres' y 'apellidos', es persona
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                registrar_empresa_rapido($data); // Si tiene 'nombre_comercial' y 'razon_social', es empresa
            }
            break;
        case 'REGISTRARPERSONAEMPLEADO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_empleado($data); // Si tiene 'nombres' y 'apellidos', es persona
            } 
            break;
        case 'REGISTRARPERSONA':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_persona($data); // Si tiene 'nombres' y 'apellidos', es persona
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                registrar_empresa($data); // Si tiene 'nombre_comercial' y 'razon_social', es empresa
            }
            break;
        case 'REGISTRAREMPLEADO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_empleado_trabajador($data); // Si tiene 'nombres' y 'apellidos', es persona
            } 
            break;
        case 'ACTUALIZAREMPLEADO':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                actualizar_empleado_trabajador($data); // Si tiene 'nombres' y 'apellidos', es persona
            } 
            break;
        case 'ACTUALIZARPERSONA':
            $data = json_decode($_POST["data"], true); // Decodificar JSON
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                actualizar_persona($data); // Si tiene 'nombres' y 'apellidos', es persona
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                actualizar_empresa($data); // Si tiene 'nombre_comercial' y 'razon_social', es empresa
            }
            break;
        case 'BLOQUEARPERSONA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_persona($id, $accion);
            break;
        case 'DESBLOQUEARPERSONA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_persona($id, $accion);
            break;
        case 'ELIMINARPERSONA':
            $id = $_POST["id"]; // Decodificar JSON
            eliminar_persona($id);
            break;
       
    }
}


function consultarPersona($id)
{
    global $conectar;

    try {
        // Cambiado INNER JOIN por LEFT JOIN para que funcione con personas sin usuario
        $orden = $conectar->prepare("
        SELECT p.id,
        p.numero_documento, 
        p.tipo_persona,
        p.condicion, 
        p.nombres,
        p.apellidos,
        p.fecha_nacimiento,
        p.telefonofijo,
        p.telefonomovil,
        p.email,
        p.direccion,
        p.nombre_comercial, 
        p.razon_social,
        p.deleted_at,
        u.username,
        u.sueldo,
        u.cantidad_horas_trabajo as horas,
        u.cantidad_dias_semana as dias
        FROM persona p
        LEFT JOIN usuario u ON u.persona_id = p.id
        WHERE p.id = :id");
        
        $orden->bindParam(":id", $id);
        $orden->execute();

        $lista = $orden->fetch(PDO::FETCH_ASSOC);
        if ($lista) {
            // Responder con un objeto que contiene éxito y los datos
            echo json_encode([
                "success" => true,
                "data" => $lista
            ]);
        } else {
            // Si no se obtienen datos, responder con un mensaje de error
            echo json_encode([
                "success" => false,
                "message" => "No se encontraron datos."
            ]);
        }

    } catch (\Throwable $th) {
        echo json_encode([
            "success" => false,
            "error" => $th->getMessage()
        ]);
    }
}
function registrar_empleado_trabajador($datos = array()) {
    global $conectar;

    try {

        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
            
        }
        // Iniciar la transacción
        $conectar->beginTransaction();

        // Insertar en la tabla persona
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email, tipo_persona, direccion, condicion)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email, 'NATURAL', :direccion, :condicion);");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];

        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId(); // Obtener el ID de la persona recién insertada
        $orden->closeCursor();

        // Verificar si username y password tienen valor
        $username = !empty($datos['username']) ? $datos['username'] : null;
        $password = !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_BCRYPT) : null;

        // Asignar rol predeterminado solo si username y password existen
        $rol = ($username && $password) ? "empleado" : null;

        // Insertar en la tabla usuario con sueldo, horas y días
        $orden_usuario = $conectar->prepare("INSERT INTO usuario (persona_id, username, password, rol, sueldo, cantidad_horas_trabajo, cantidad_dias_semana)
                                             VALUES (:persona_id, :username, :password, :rol, :sueldo, :horas, :dias);");
        $orden_usuario->bindParam(":persona_id", $persona_id);
        $orden_usuario->bindParam(":username", $username, is_null($username) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":password", $password, is_null($password) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":rol", $rol, is_null($rol) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $sueldo = empty($datos['sueldo']) ? null : $datos['sueldo'];
        $horas = empty($datos['horas']) ? null : $datos['horas'];
        $dias = empty($datos['dias']) ? null : $datos['dias'];

        $orden_usuario->bindParam(":sueldo", $sueldo, is_null($sueldo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":horas", $horas, is_null($horas) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $orden_usuario->bindParam(":dias", $dias, is_null($dias) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        
        $orden_usuario->execute();
        $orden_usuario->closeCursor();

        // Confirmar la transacción
        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_persona($datos = array()) {
    global $conectar;

    try {
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
        }
        // Insertar en la tabla persona
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email,tipo_persona,direccion,condicion)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email,'NATURAL',:direccion,:condicion);");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);


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
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
        }
        // Insertar en la tabla persona (aunque sea empresa, también se inserta en persona)
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombre_comercial, razon_social, telefonomovil, email,tipo_persona,direccion,condicion)
                                     VALUES (:numero_documento, :nombre_comercial, :razon_social, :telefono_movil, :email,'JURIDICA',:direccion,:condicion);");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

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

function actualizar_empleado_trabajador($datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        // Actualizar datos en la tabla persona
        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombres = :nombres, 
            apellidos = :apellidos, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];

        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $orden->closeCursor();

        // Actualizar datos en la tabla usuario
        $orden_usuario = $conectar->prepare("UPDATE usuario SET 
            username = :username, 
            password = COALESCE(:password, password), 
            rol = :rol, 
            sueldo = :sueldo, 
            cantidad_horas_trabajo = :horas, 
            cantidad_dias_semanas = :dias
            WHERE persona_id = :persona_id");

        $orden_usuario->bindParam(":persona_id", $datos['id']);

        // Verificar si username y password tienen valor
        $username = !empty($datos['username']) ? $datos['username'] : null;
        $password = !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_BCRYPT) : null;

        // Asignar rol predeterminado solo si username y password existen
        $rol = ($username && $password) ? "empleado" : null;

        $orden_usuario->bindParam(":username", $username, is_null($username) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":password", $password, is_null($password) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":rol", $rol, is_null($rol) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        // Actualizar sueldo, horas y días
        $sueldo = empty($datos['sueldo']) ? null : $datos['sueldo'];
        $horas = empty($datos['horas']) ? null : $datos['horas'];
        $dias = empty($datos['dias']) ? null : $datos['dias'];

        $orden_usuario->bindParam(":sueldo", $sueldo, is_null($sueldo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":horas", $horas, is_null($horas) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $orden_usuario->bindParam(":dias", $dias, is_null($dias) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        
        $orden_usuario->execute();
        $orden_usuario->closeCursor();

        // Confirmar la transacción
        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Persona y usuario actualizados correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_persona( $datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombres = :nombres, 
            apellidos = :apellidos, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];

        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Persona actualizada correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_empresa( $datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombre_comercial = :nombre_comercial, 
            razon_social = :razon_social, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];

        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Empresa actualizada correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_empresa: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}



function registrar_persona_rapido($datos = array()) {
    global $conectar;

    try {
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
        }

        // Insertar en la tabla persona
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email,tipo_persona,condicion)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email,'NATURAL','CLIENTE');");
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

function registrar_empleado($datos = array()) {
    global $conectar;

    try {
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
        }
        // Insertar en la tabla persona
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email,tipo_persona,condicion)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email,'NATURAL','EMPLEADO');");
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


function registrar_empresa_rapido($datos = array()) {
    global $conectar;

    try {
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento");
        $verificar->bindParam(":numero_documento", $datos['numero_documento']);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe > 0) {
            echo json_encode(["error" => true, "message" => "El número de documento ya está registrado."]);
            return;
        }
        // Insertar en la tabla persona (aunque sea empresa, también se inserta en persona)
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombre_comercial, razon_social, telefonomovil, email,tipo_persona,condicion)
                                     VALUES (:numero_documento, :nombre_comercial, :razon_social, :telefono_movil, :email,'JURIDICA','EMPRESA');");
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

function toggle_estado_persona($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarPersona = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE id = :id");
        $verificarPersona->bindParam(":id", $id);
        $verificarPersona->execute();
        $personaExistente = $verificarPersona->fetchColumn();

        if ($personaExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Persona no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEARPERSONA") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE persona SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEARPERSONA") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE persona SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado de la persona actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function eliminar_persona($id) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarPersona = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE id = :id");
        $verificarPersona->bindParam(":id", $id);
        $verificarPersona->execute();
        $personaExistente = $verificarPersona->fetchColumn();

        if ($personaExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Persona no encontrado."]);
            return;
        }

        // Verificar la relación con la tabla compra
        $verificarRelacionCompra = $conectar->prepare("SELECT COUNT(*) FROM compra WHERE cliente_id = :id");
        $verificarRelacionVenta = $conectar->prepare("SELECT COUNT(*) FROM venta WHERE proveedor = :id");
        $verificarRelacionUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE persona_id = :id"); // Verificación con la tabla usuario

        // Asociar el parámetro :id
        $verificarRelacionCompra->bindParam(":id", $id);
        $verificarRelacionVenta->bindParam(":id", $id);
        $verificarRelacionUsuario->bindParam(":id", $id);

        // Ejecutar las consultas
        $verificarRelacionCompra->execute();
        $verificarRelacionVenta->execute();
        $verificarRelacionUsuario->execute();

        // Obtener los resultados de las consultas
        $relacionCompra = $verificarRelacionCompra->fetchColumn();
        $relacionVenta = $verificarRelacionVenta->fetchColumn();
        $relacionUsuario = $verificarRelacionUsuario->fetchColumn(); // Resultado de la relación con usuario

        // Verificar si hay alguna relación en las tres tablas
        if ($relacionCompra > 0 || $relacionVenta > 0 || $relacionUsuario > 0) {
            // Si hay relación, retornar un error
            echo json_encode(["error" => true, "message" => "No se puede eliminar a esta persona porque está asociada con compras, ventas o usuarios activos en el sistema."]);
            return;
        }

        // Eliminar al usuario si no hay relaciones
        $sql = "DELETE FROM usuario WHERE id = :id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Persona eliminado con éxito."]);

    } catch (\Throwable $th) {
        error_log("Error en eliminar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}
