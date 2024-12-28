function showNotification(estado) {
    // Configurar el contenido de la notificación
    var content = {};
    var state = "";              // Tipo de estado (success, error, etc.)
    var placementFrom = "bottom"; // Posición vertical
    var placementAlign = "right"; // Posición horizontal

    // Personalizar contenido según el estado
    switch (estado) {
        case 'success':
            content.message = "Operación exitosa.";
            content.title = "¡Éxito!";
            content.icon = "fa fa-check-circle"; // Ícono para éxito
            state = "success";
            break;
        case 'error':
            content.message = "Ocurrió un error inesperado.";
            content.title = "¡Error!";
            content.icon = "fa fa-times-circle"; // Ícono para error
            state = "danger";
            break;
        case 'warning':
            content.message = "Advertencia: Revisa los datos.";
            content.title = "¡Advertencia!";
            content.icon = "fa fa-exclamation-triangle"; // Ícono para advertencia
            state = "warning";
            break;
        case 'info':
            content.message = "Este es un mensaje informativo.";
            content.title = "Información";
            content.icon = "fa fa-info-circle"; // Ícono para información
            state = "info";
            break;
        default:
            content.message = "Estado desconocido.";
            content.title = "Notificación";
            content.icon = "fa fa-question-circle"; // Ícono por defecto
            state = "info";
    }

    // Mostrar la notificación
    $.notify(content, {
        type: state, // Estado
        placement: {
            from: placementFrom, // Posición vertical
            align: placementAlign // Posición horizontal
        },
        time: 1000,   // Duración de la animación
        delay: 3000,  // Tiempo para ocultar (ms)
    });
}

