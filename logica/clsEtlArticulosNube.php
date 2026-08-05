<?php
/**
 * clsEtlArticulosNube.php
 * -----------------------------------------------------------------------
 * Archivo dedicado EXCLUSIVAMENTE a ejecutar el proceso ETL que migra la
 * tabla "articulo" hacia el esquema "bdnubelibrodri" (usado por Power BI).
 *
 * Separado de clssConsultas.php para mantener esta lógica aislada,
 * fácil de mantener y de dar seguimiento (logs, permisos, etc.)
 *
 * Requiere: bd.php (debe definir la variable $conectar como PDO)
 * -----------------------------------------------------------------------
 */

include("bd.php");

header('Content-Type: application/json; charset=utf-8');

set_time_limit(300); // o el tiempo que necesites, en segundos
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["accion"]) && $_POST["accion"] === "EJECUTARETLARTICULOSNUBE") {
    echo json_encode(fnEjecutarEtlArticulosNube());
    exit;
}

// Si llega sin la acción esperada, respondemos con error controlado
echo json_encode([
    'estado'  => false,
    'mensaje' => 'Acción no reconocida o no enviada.'
]);
exit;


/**
 * Ejecuta la función de Postgres fn_etl_articulo() y normaliza la respuesta
 * para que el frontend siempre reciba: { estado: bool, mensaje: string, proceso: string }
 */
function fnEjecutarEtlArticulosNube(): array
{
    global $conectar;

    try {
        $stmt = $conectar->query("SELECT fn_etl_articulo() AS resultado;");
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !isset($row['resultado'])) {
            return [
                'proceso' => 'ESQUEMA ETL ARTICULO',
                'estado'  => false,
                'mensaje' => 'ERROR: la función no devolvió resultado.'
            ];
        }

        // fn_etl_articulo() devuelve tipo "json" -> PDO lo entrega como string
        $data = json_decode($row['resultado'], true);

        if (!is_array($data)) {
            return [
                'proceso' => 'ESQUEMA ETL ARTICULO',
                'estado'  => false,
                'mensaje' => 'ERROR: no se pudo interpretar la respuesta de la función.'
            ];
        }

        // Normalizamos por si alguna clave viniera ausente
        return [
            'proceso' => $data['proceso'] ?? 'ESQUEMA ETL ARTICULO',
            'estado'  => (bool) ($data['estado'] ?? false),
            'mensaje' => $data['mensaje'] ?? ''
        ];

    } catch (PDOException $e) {
        // Aquí caen errores de conexión, timeout, permisos, etc.
        return [
            'proceso' => 'ESQUEMA ETL ARTICULO',
            'estado'  => false,
            'mensaje' => 'ERROR DE CONEXIÓN: ' . $e->getMessage()
        ];
    }
}