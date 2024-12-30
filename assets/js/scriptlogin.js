async function hashPassword(password) {
    const encoder = new TextEncoder();
    const data = encoder.encode(password);
    const hash = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hash))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
}

async function iniciarSesion() {
    var usserLogin = document.getElementById("user").value;
    var passLogin = document.getElementById("password").value;
    var errorUserLog = document.getElementById("errorUserLog");
    var errorPassLog = document.getElementById("errorPassLog");
    //const hashedPassword = await hashPassword(passLogin);

    errorUserLog.innerHTML = "";
    errorPassLog.innerHTML = "";

    if(usserLogin.trim() === "" && passLogin.trim() === ""){
        errorPassLog.innerHTML = "El campo 'Contraseña' es obligatorio.";
        errorUserLog.innerHTML = "El campo 'Usuario' es obligatorio.";
        return

    }
    if (usserLogin.trim() === "") {
        errorUserLog.innerHTML = "El campo 'Usuario' es obligatorio.";
        return; 
    }

    if (passLogin.trim() === "") {
        errorPassLog.innerHTML = "El campo 'Contraseña' es obligatorio.";
        return; 
    }

    // Si ambos campos tienen datos, realizar el AJAX
    $.ajax({
        method: "POST",
        url: "logica/clsslogin.php",
        data: {
            "accion": "LOGIN",
            "user": usserLogin,
            "password": passLogin
        }
    }).done(async function (text) {
        console.log(text);
        try {

            var userData = JSON.parse(text);
            if(userData){
                if(userData.error){
                    errorPassLog.innerHTML = userData.error;
                    errorUserLog.innerHTML = userData.error;
    
                }else{
                    window.location.href = "index.php"; 
                }
            }
        } catch(e) {
            console.log(e);
            errorPassLog.innerHTML = "Error al iniciar sesión.";
        }

    });
}

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("form-login");
    loginForm.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            iniciarSesion();
        }
    });
});