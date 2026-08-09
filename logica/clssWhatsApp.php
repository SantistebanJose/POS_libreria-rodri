<?php
/**
 * Clase para manejar el envío de comprobantes por WhatsApp
 * 
 * Este archivo procesa las solicitudes de envío de comprobantes de venta
 * (boletas o facturas) a través de WhatsApp usando una API
 */

// Incluir archivos necesarios de tu sistema
// require_once 'conexion.php'; // Descomentar y ajustar según tu estructura

header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método no permitido'
    ]);
    exit;
}

// Obtener la acción solicitada
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion === 'ENVIAR_COMPROBANTE_WHATSAPP') {
    enviarComprobanteWhatsApp();
}

/**
 * Función principal para enviar el comprobante por WhatsApp
 */
function enviarComprobanteWhatsApp() {
    try {
        // Obtener parámetros
        $idVenta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;
        $numeroTelefono = isset($_POST['numero_telefono']) ? $_POST['numero_telefono'] : '';
        $tipoComprobante = isset($_POST['tipo_comprobante']) ? $_POST['tipo_comprobante'] : 'boleta';
        
        // Validar parámetros
        if ($idVenta <= 0) {
            throw new Exception('ID de venta inválido');
        }
        
        if (empty($numeroTelefono)) {
            throw new Exception('Número de teléfono no proporcionado');
        }
        
        // Limpiar y formatear el número de teléfono
        $numeroTelefono = limpiarNumeroTelefono($numeroTelefono);
        
        if (!validarNumeroTelefono($numeroTelefono)) {
            throw new Exception('Número de teléfono inválido');
        }
        
        // Obtener información de la venta desde la base de datos
        // $infoVenta = obtenerInformacionVenta($idVenta);
        
        // Construir el mensaje para WhatsApp
        $mensaje = construirMensajeComprobante($idVenta, $tipoComprobante);
        
        // Enviar mensaje por WhatsApp
        $resultado = enviarMensajeWhatsApp($numeroTelefono, $mensaje, $idVenta);
        
        if ($resultado) {
            // Registrar el envío en la base de datos (opcional)
            // registrarEnvioWhatsApp($idVenta, $numeroTelefono);
            
            echo json_encode([
                'success' => true,
                'mensaje' => 'Comprobante enviado correctamente por WhatsApp'
            ]);
        } else {
            throw new Exception('No se pudo enviar el mensaje por WhatsApp');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}

/**
 * Limpia y formatea el número de teléfono
 * @param string $numero
 * @return string
 */
function limpiarNumeroTelefono($numero) {
    // Remover espacios, guiones y paréntesis
    $numero = preg_replace('/[^0-9]/', '', $numero);
    
    // Si el número tiene 9 dígitos (formato peruano), agregar código de país
    if (strlen($numero) == 9) {
        $numero = '51' . $numero; // Código de país de Perú
    }
    
    return $numero;
}

/**
 * Valida que el número de teléfono tenga el formato correcto
 * @param string $numero
 * @return bool
 */
function validarNumeroTelefono($numero) {
    // Debe tener entre 11 y 15 dígitos (con código de país)
    return strlen($numero) >= 11 && strlen($numero) <= 15 && ctype_digit($numero);
}

/**
 * Construye el mensaje que se enviará por WhatsApp
 * @param int $idVenta
 * @param string $tipoComprobante
 * @return string
 */
function construirMensajeComprobante($idVenta, $tipoComprobante) {
    $nombreNegocio = "Librería Bazar Rodri"; // Cambiar por el nombre de tu negocio
    $tipoDoc = ($tipoComprobante === 'factura') ? 'Factura' : 'Boleta';
    
    // URL del ticket/comprobante en Render (sin subcarpeta)
    $urlComprobante = "https://libreria-rodri-pos.onrender.com/ticket.php?id=" . $idVenta;
    
    $mensaje = "🧾 *{$tipoDoc} de Venta*\n\n";
    $mensaje .= "Hola! Gracias por su compra en *{$nombreNegocio}*\n\n";
    $mensaje .= "Le enviamos su comprobante de venta #{$idVenta}\n\n";
    $mensaje .= "📄 Puede ver su {$tipoDoc} en el siguiente enlace:\n";
    $mensaje .= $urlComprobante . "\n\n";
    $mensaje .= "✅ Gracias por su preferencia!\n";
    $mensaje .= "📞 Para consultas, contáctenos";
    return $mensaje;
}

/**
 * Envía el mensaje por WhatsApp usando una API
 * 
 * NOTA IMPORTANTE: Aquí debes implementar la integración con tu proveedor de WhatsApp API
 * Opciones populares:
 * 1. WhatsApp Business API (oficial)
 * 2. Twilio API
 * 3. Ultramsg
 * 4. Maytapi
 * 5. WhatsMate
 * 
 * @param string $numeroTelefono
 * @param string $mensaje
 * @param int $idVenta
 * @return bool
 */
function enviarMensajeWhatsApp($numeroTelefono, $mensaje, $idVenta) {
    // MÉTODO 1: Usando WhatsApp Web (redirección simple - no envía automáticamente)
    // Este método abre WhatsApp Web con el mensaje prellenado
    // El usuario debe hacer clic en enviar manualmente
    // return true; // Solo retornar true si quieres usar este método
    
    // MÉTODO 2: Usando una API de WhatsApp (RECOMENDADO)
    // Ejemplo con Ultramsg (debes registrarte y obtener tu token)
    /*
    $instance_id = "instance12345"; // Tu instance ID
    $token = "tu_token_aqui"; // Tu token de API
    $url = "https://api.ultramsg.com/{$instance_id}/messages/chat";
    
    $params = [
        'token' => $token,
        'to' => $numeroTelefono,
        'body' => $mensaje
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode == 200;
    */
    
    // MÉTODO 3: Usando Twilio
    /*
    require_once 'vendor/autoload.php'; // SDK de Twilio
    use Twilio\Rest\Client;
    
    $account_sid = 'tu_account_sid';
    $auth_token = 'tu_auth_token';
    $twilio_whatsapp_number = 'whatsapp:+14155238886';
    
    $client = new Client($account_sid, $auth_token);
    
    try {
        $message = $client->messages->create(
            "whatsapp:+" . $numeroTelefono,
            [
                "from" => $twilio_whatsapp_number,
                "body" => $mensaje
            ]
        );
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar WhatsApp: " . $e->getMessage());
        return false;
    }
    */
    
    // IMPLEMENTACIÓN TEMPORAL: Para probar sin API
    // Comentar esto y descomentar uno de los métodos de arriba cuando tengas una API configurada
    
    // Simular envío exitoso para pruebas
    // En producción, esto debe ser reemplazado por una API real
    sleep(1); // Simular tiempo de procesamiento
    return true; // Cambiar a false si quieres probar el flujo de error
}

/**
 * Obtiene información detallada de la venta desde la base de datos
 * @param int $idVenta
 * @return array|null
 */
function obtenerInformacionVenta($idVenta) {
    // Implementar la consulta a la base de datos
    // Ejemplo:
    /*
    global $conn; // Tu conexión a la base de datos
    
    $sql = "SELECT v.*, p.nombres, p.apellidos, p.numero_documento 
            FROM ventas v 
            LEFT JOIN personas p ON v.cliente_id = p.id 
            WHERE v.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idVenta);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
    */
    
    return [
        'id' => $idVenta,
        'monto_total' => 0,
        'fecha' => date('Y-m-d H:i:s')
    ];
}

/**
 * Registra el envío del WhatsApp en la base de datos para llevar un control
 * @param int $idVenta
 * @param string $numeroTelefono
 * @return bool
 */
function registrarEnvioWhatsApp($idVenta, $numeroTelefono) {
    // Implementar el registro en la base de datos
    // Ejemplo:
    /*
    global $conn;
    
    $sql = "INSERT INTO envios_whatsapp (venta_id, numero_telefono, fecha_envio, estado) 
            VALUES (?, ?, NOW(), 'enviado')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $idVenta, $numeroTelefono);
    
    return $stmt->execute();
    */
    
    return true;
}

?>