<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorReservaWeb($accion);
}

function controladorReservaWeb($accion)
{
    switch ($accion) {
        case 'ACTUALIZARESTADO':
            $reserva_id = isset($_POST["reserva_id"]) ? intval($_POST["reserva_id"]) : 0;
            $nuevo_estado = isset($_POST["nuevo_estado"]) ? trim($_POST["nuevo_estado"]) : '';
            fn_actualizar_estado_reserva($reserva_id, $nuevo_estado);
            break;
        
        case 'OBTENERRESERVAS':
            fn_obtener_reservas_web();
            break;
        
        case 'BUSCAR_ARTICULO':
            $termino = isset($_POST["termino"]) ? trim($_POST["termino"]) : '';
            fn_buscar_articulo($termino);
            break;
        
        case 'ACTUALIZARDETALLE':
            $detalle_id = isset($_POST["detalle_id"]) ? intval($_POST["detalle_id"]) : 0;
            $reserva_id = isset($_POST["reserva_id"]) ? intval($_POST["reserva_id"]) : 0;
            $cambios = isset($_POST["cambios"]) ? json_decode($_POST["cambios"], true) : [];
            fn_actualizar_detalle_reserva($detalle_id, $reserva_id, $cambios);
            break;
        
        case 'ELIMINARDETALLE':
            $detalle_id = isset($_POST["detalle_id"]) ? intval($_POST["detalle_id"]) : 0;
            $reserva_id = isset($_POST["reserva_id"]) ? intval($_POST["reserva_id"]) : 0;
            fn_eliminar_detalle_reserva($detalle_id, $reserva_id);
            break;
        
        case 'AGREGARDETALLE':
            $datos = [
                'reserva_id' => isset($_POST["reserva_id"]) ? intval($_POST["reserva_id"]) : 0,
                'articulo_id' => isset($_POST["articulo_id"]) ? intval($_POST["articulo_id"]) : 0,
                'cantidad' => isset($_POST["cantidad"]) ? intval($_POST["cantidad"]) : 1,
                'precio_unitario' => isset($_POST["precio_unitario"]) ? floatval($_POST["precio_unitario"]) : 0,
                'subtotal' => isset($_POST["subtotal"]) ? floatval($_POST["subtotal"]) : 0
            ];
            fn_agregar_detalle_reserva($datos);
            break;
        
        case 'ACTUALIZARNOTAS':
            $reserva_id = isset($_POST["reserva_id"]) ? intval($_POST["reserva_id"]) : 0;
            $notas = isset($_POST["notas"]) ? trim($_POST["notas"]) : '';
            fn_actualizar_notas_reserva($reserva_id, $notas);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
}

function fn_actualizar_estado_reserva($reserva_id, $nuevo_estado)
{
    global $conectar;

    // Validar ID
    if ($reserva_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de reserva inválido'
        ]);
        return;
    }

    // Estados permitidos
    $estados_permitidos = [
        'pendiente',
        'confirmado',
        'preparando',
        'listo',
        'entregado',
        'cancelado'
    ];

    if (!in_array($nuevo_estado, $estados_permitidos)) {
        echo json_encode([
            'success' => false,
            'message' => 'Estado no válido'
        ]);
        return;
    }

    try {
        // Iniciar transacción
        $conectar->beginTransaction();

        // Obtener datos completos de la reserva
        $orden = $conectar->prepare("
            SELECT id, estado, total, json_detalles, telefonomovil_cliente, usuario_id 
            FROM view_foreingdatabase_reservas_web
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $reserva = $orden->fetch(PDO::FETCH_ASSOC);
        $json_reserva_ctmre = json_encode($reserva);
        $orden->closeCursor();

        if (!$reserva) {
            throw new Exception("No se encontró la reserva con ID: $reserva_id");
        }

        // Actualizar estado de la reserva
        $orden = $conectar->prepare("
            UPDATE bdnubelibrodri.reserva_web 
            SET estado = :nuevo_estado 
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":nuevo_estado", $nuevo_estado, PDO::PARAM_STR);
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        // Si el estado es "confirmado", crear la venta
        $venta_id = null;
        if ($nuevo_estado === 'listo') {
            // Insertar en venta
            $orden = $conectar->prepare("
                INSERT INTO venta (
                    fecha,
                    estado_pago,
                    total,
                    estado_venta,
                    monto_original,
                    monto_venta_final,
                    atencion_final_usuario,
                    numerotelefono_cliente_venta,
                    estado_final,
                    fecha_fin_transaccion,
                    js_detalle_venta
                ) VALUES (
                    CURRENT_DATE,
                    'P',
                    :total,
                    'VR',
                    :total,
                    :total,
                    'ONLINE WEB',
                    :telefono,
                    'PAGADO',
                    CURRENT_TIMESTAMP,
                    :json_detalle_venta
                )
            ");
            
            $orden->bindParam(":total", $reserva['total'], PDO::PARAM_STR);
            $orden->bindParam(":telefono", $reserva['telefonomovil_cliente'], PDO::PARAM_STR);
            //$orden->bindParam(":usuario_id", $reserva['usuario_id'], PDO::PARAM_INT);
            $orden->bindParam(":json_detalle_venta", $json_reserva_ctmre, PDO::PARAM_STR);
            $orden->execute();
            $venta_id = $conectar->lastInsertId();
            $orden->closeCursor();

            // Decodificar json_detalles e insertar en rel_venta_articulo
            $detalles = json_decode($reserva['json_detalles'], true);
            
            if (is_array($detalles) && count($detalles) > 0) {
                foreach ($detalles as $detalle) {
                    $orden = $conectar->prepare("
                        INSERT INTO rel_venta_articulo (
                            venta_id,
                            articulo_id,
                            precio_unitario_articulo,
                            cantidad,
                            sub_total,
                            nota_archivo,
                            movimiento_id
                        ) VALUES (
                            :venta_id,
                            :articulo_id,
                            :precio_unitario,
                            :cantidad,
                            :subtotal,
                            'RESERVA WEB',
                            1
                        )
                    ");
                    
                    $articulo_id = isset($detalle['articulo_id']) && $detalle['articulo_id'] > 0 
                        ? $detalle['articulo_id'] 
                        : null;
                    
                    $orden->bindParam(":venta_id", $venta_id, PDO::PARAM_INT);
                    $orden->bindParam(":articulo_id", $articulo_id, 
                        $articulo_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $orden->bindParam(":precio_unitario", $detalle['precio_unitario'], PDO::PARAM_STR);
                    $orden->bindParam(":cantidad", $detalle['cantidad'], PDO::PARAM_INT);
                    $orden->bindParam(":subtotal", $detalle['subtotal'], PDO::PARAM_STR);
                    $orden->execute();
                    $orden->closeCursor();
                }
            }
            $orden = $conectar->prepare("SELECT fn_update_stock_nube(:articulo_id);");
            $orden->bindParam(":articulo_id", $articulo_id, 
                        $articulo_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $orden->execute();
            $orden->closeCursor();
            //select * from fn_etl_articulo();
        }

        // Confirmar transacción
        $conectar->commit();

        $mensaje = 'Estado actualizado correctamente a: ' . ucfirst($nuevo_estado);
        if ($venta_id) {
            $mensaje .= ' - Venta #' . $venta_id . ' creada exitosamente';
        }

        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'reserva_id' => $reserva_id,
            'nuevo_estado' => $nuevo_estado,
            'venta_id' => $venta_id
        ]);

    } catch (\Throwable $th) {
        // Rollback en caso de error
        $conectar->rollBack();
        error_log("Error al actualizar estado de reserva: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar: ' . $th->getMessage()
        ]);
    }
}

function fn_obtener_reservas_web()
{
    global $conectar;

    try {
        $orden = $conectar->prepare("
            SELECT 
                id,
                usuario_id,
                numero_documento,
                nombres_cliente,
                apelldios_cliente,
                telefonomovil_cliente,
                fechareserva,
                estado,
                total,
                notas,
                json_detalles
            FROM bdnubelibrodri.reserva_web
            ORDER BY fechareserva DESC
        ");
        $orden->execute();
        $reservas = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        echo json_encode([
            'success' => true,
            'data' => $reservas
        ]);

    } catch (\Throwable $th) {
        error_log("Error al obtener reservas: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener reservas: ' . $th->getMessage()
        ]);
    }
}

function fn_buscar_articulo($termino)
{
    global $conectar;

    try {
        $orden = $conectar->prepare("
            SELECT id, nombre, precio_venta, stock
            FROM articulo
            WHERE nombre ILIKE :termino
            AND deleted_at IS NULL
            AND stock > 0
            ORDER BY nombre ASC
            LIMIT 10
        ");
        $busqueda = '%' . $termino . '%';
        $orden->bindParam(":termino", $busqueda, PDO::PARAM_STR);
        $orden->execute();
        $articulos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        echo json_encode([
            'success' => true,
            'data' => $articulos
        ]);

    } catch (\Throwable $th) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al buscar: ' . $th->getMessage()
        ]);
    }
}

function fn_actualizar_detalle_reserva($detalle_id, $reserva_id, $cambios)
{
    global $conectar;

    try {
        $conectar->beginTransaction();

        // Obtener el detalle actual
        $orden = $conectar->prepare("
            SELECT json_detalles, total 
            FROM bdnubelibrodri.reserva_web 
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $reserva = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        if (!$reserva) {
            throw new Exception("Reserva no encontrada");
        }

        // Decodificar json_detalles
        $detalles = json_decode($reserva['json_detalles'], true);
        $total_anterior = floatval($reserva['total']);
        
        // Buscar y actualizar el detalle específico
        $encontrado = false;
        $diferencia_total = 0;
        
        foreach ($detalles as &$detalle) {
            if ($detalle['id'] == $detalle_id) {
                $subtotal_anterior = floatval($detalle['subtotal']);
                
                // Actualizar campos
                if (isset($cambios['cantidad'])) {
                    $detalle['cantidad'] = intval($cambios['cantidad']);
                }
                if (isset($cambios['precio_unitario'])) {
                    $detalle['precio_unitario'] = floatval($cambios['precio_unitario']);
                }
                if (isset($cambios['subtotal'])) {
                    $detalle['subtotal'] = floatval($cambios['subtotal']);
                }
                
                $diferencia_total = floatval($detalle['subtotal']) - $subtotal_anterior;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            throw new Exception("Detalle no encontrado en json_detalles");
        }

        // Actualizar json_detalles y total
        $nuevo_total = $total_anterior + $diferencia_total;
        $json_actualizado = json_encode($detalles);

        $orden = $conectar->prepare("
            UPDATE bdnubelibrodri.reserva_web 
            SET json_detalles = :json_detalles,
                total = :nuevo_total
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":json_detalles", $json_actualizado, PDO::PARAM_STR);
        $orden->bindParam(":nuevo_total", $nuevo_total, PDO::PARAM_STR);
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Detalle actualizado correctamente',
            'nuevo_total' => number_format($nuevo_total, 2)
        ]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error al actualizar detalle: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar: ' . $th->getMessage()
        ]);
    }
}

function fn_eliminar_detalle_reserva($detalle_id, $reserva_id)
{
    global $conectar;

    try {
        $conectar->beginTransaction();

        // Obtener la reserva
        $orden = $conectar->prepare("
            SELECT json_detalles, total 
            FROM bdnubelibrodri.reserva_web 
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $reserva = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        if (!$reserva) {
            throw new Exception("Reserva no encontrada");
        }

        // Decodificar y filtrar detalles
        $detalles = json_decode($reserva['json_detalles'], true);
        $subtotal_eliminado = 0;
        $nuevos_detalles = [];

        foreach ($detalles as $detalle) {
            if ($detalle['id'] == $detalle_id) {
                $subtotal_eliminado = floatval($detalle['subtotal']);
            } else {
                $nuevos_detalles[] = $detalle;
            }
        }

        // Actualizar
        $nuevo_total = floatval($reserva['total']) - $subtotal_eliminado;
        $json_actualizado = json_encode($nuevos_detalles);

        $orden = $conectar->prepare("
            UPDATE bdnubelibrodri.reserva_web 
            SET json_detalles = :json_detalles,
                total = :nuevo_total
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":json_detalles", $json_actualizado, PDO::PARAM_STR);
        $orden->bindParam(":nuevo_total", $nuevo_total, PDO::PARAM_STR);
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Artículo eliminado correctamente',
            'nuevo_total' => number_format($nuevo_total, 2)
        ]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error al eliminar detalle: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar: ' . $th->getMessage()
        ]);
    }
}

function fn_agregar_detalle_reserva($datos)
{
    global $conectar;

    try {
        $conectar->beginTransaction();

        // Obtener la reserva
        $orden = $conectar->prepare("
            SELECT json_detalles, total 
            FROM bdnubelibrodri.reserva_web 
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":reserva_id", $datos['reserva_id'], PDO::PARAM_INT);
        $orden->execute();
        $reserva = $orden->fetch(PDO::FETCH_ASSOC);
        $orden->closeCursor();

        if (!$reserva) {
            throw new Exception("Reserva no encontrada");
        }

        // Decodificar detalles actuales
        $detalles = json_decode($reserva['json_detalles'], true);
        
        // Obtener el próximo ID
        $max_id = 0;
        foreach ($detalles as $detalle) {
            if ($detalle['id'] > $max_id) {
                $max_id = $detalle['id'];
            }
        }
        $nuevo_id = $max_id + 1;

        // Agregar nuevo detalle
        $nuevo_detalle = [
            'id' => $nuevo_id,
            'articulo_id' => $datos['articulo_id'],
            'cantidad' => $datos['cantidad'],
            'precio_unitario' => $datos['precio_unitario'],
            'subtotal' => $datos['subtotal']
        ];
        $detalles[] = $nuevo_detalle;

        // Actualizar
        $nuevo_total = floatval($reserva['total']) + floatval($datos['subtotal']);
        $json_actualizado = json_encode($detalles);

        $orden = $conectar->prepare("
            UPDATE bdnubelibrodri.reserva_web 
            SET json_detalles = :json_detalles,
                total = :nuevo_total
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":json_detalles", $json_actualizado, PDO::PARAM_STR);
        $orden->bindParam(":nuevo_total", $nuevo_total, PDO::PARAM_STR);
        $orden->bindParam(":reserva_id", $datos['reserva_id'], PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Artículo agregado correctamente',
            'detalle_id' => $nuevo_id,
            'nuevo_total' => number_format($nuevo_total, 2)
        ]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error al agregar detalle: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al agregar: ' . $th->getMessage()
        ]);
    }
}

function fn_actualizar_notas_reserva($reserva_id, $notas)
{
    global $conectar;

    try {
        $orden = $conectar->prepare("
            UPDATE bdnubelibrodri.reserva_web 
            SET notas = :notas 
            WHERE id = :reserva_id
        ");
        $orden->bindParam(":notas", $notas, PDO::PARAM_STR);
        $orden->bindParam(":reserva_id", $reserva_id, PDO::PARAM_INT);
        $orden->execute();
        $orden->closeCursor();

        echo json_encode([
            'success' => true,
            'message' => 'Notas actualizadas correctamente'
        ]);

    } catch (\Throwable $th) {
        error_log("Error al actualizar notas: " . $th->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar notas: ' . $th->getMessage()
        ]);
    }
}
?>