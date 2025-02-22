<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Restablecer Contraseña</h2>
        <form id="form-cambiar-contraseña">
            <div class="mb-4">
                <label for="dni" class="block text-gray-600 mb-1">DNI</label>
                <input type="text" id="dni" name="dni" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-600 mb-1">Nueva Contraseña</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <button type="button" onclick="cambiar_contraseña()" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">Guardar</button>
        </form>
        <div class="text-center mt-4">
            <a href="index.php" class="text-blue-500 hover:underline">Volver al inicio</a>
        </div>
    </div>

    <script>
        function cambiar_contraseña() {
            let dni = document.getElementById('dni').value;
            let password = document.getElementById('password').value;

            if (!dni || !password) {
                alert("Por favor, complete todos los campos.");
                return;
            }

            $.ajax({
                method: "POST",
                url: "logica/clsslogin.php",
                data: {
                    "accion": "ALTERCONTRASEÑA",
                    "dni": dni,
                    "password": password
                }
            }).done(function (response) {
                try {
                    console.log(response)
                    const jsonResponse = JSON.parse(response);
                    if (jsonResponse.success) {
                        alert("Contraseña actualizada con éxito");
                        window.location.href = "login.php"; // Redireccionar a inicio
                    } else {
                        alert(jsonResponse.message || "Error desconocido");
                    }
                } catch (e) {
                    alert("Error en la respuesta del servidor.");
                }
            }).fail(function (error) {
                console.error("Error:", error.responseText);
                alert("Hubo un error al actualizar la contraseña.");
            });
        }
    </script>
</body>
</html>
