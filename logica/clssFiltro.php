<?php
include("bd.php");

if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
    controladorFiltro($accion);
}

function controladorFiltro($accion){
    switch($accion){
        case 'FILTROPERSONA':
            $data = $_POST["data"];
           
            consultapersonaventa($data);
            break;
        case 'CAMBIARCONTRASEÑA':
            break;

    }
}

function consultapersonaventa($data): void
{
    global $conectar;

    try {
        // Consulta SQL con la función LOWER para hacer la comparación insensible a mayúsculas y minúsculas
        $query = $conectar->prepare("
            SELECT id, 
                CONCAT(numero_documento, ' - ', nombres, ' ', apellidos) AS persona_concatenada,
                COALESCE(NULLIF(telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(email, ''), 'Sin correo') AS email
            FROM persona
            WHERE LOWER(numero_documento) LIKE LOWER(:data)
               OR LOWER(nombres) LIKE LOWER(:data)
               OR LOWER(apellidos) LIKE LOWER(:data)
            LIMIT 10;
        ");

        // Pasamos el parámetro con los signos de porcentaje en PHP
        $likeData = "%" . $data . "%";

        // Usamos bindValue para pasar el parámetro
        $query->bindValue(':data', $likeData, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los resultados como JSON
        echo json_encode($result);
    } catch (\Throwable $th) {
        // En caso de error, devolver un JSON con el mensaje
        echo json_encode([
            "error" => true,
            "message" => $th->getMessage()
        ]);
    }
}
