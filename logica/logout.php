<?php
session_start(); // Iniciar o reanudar la sesión

// Eliminar todas las variables de sesión
session_unset(); 

// Destruir la sesión actual
session_destroy(); 


// Redirigir al index.php
header("Location: ../login.php");


exit();
