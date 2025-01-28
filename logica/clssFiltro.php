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
WITH words AS (
  SELECT unnest(string_to_array(LOWER(:data), ' ')) AS word
)
SELECT id, 
       CONCAT(numero_documento, ' - ', nombres, ' ', apellidos) AS persona_concatenada
FROM persona
WHERE EXISTS (
    SELECT 1
    FROM words w
    WHERE 
        LOWER(nombres) ILIKE CONCAT('%', w.word, '%')
        OR LOWER(apellidos) ILIKE CONCAT('%', w.word, '%')
        OR LOWER(numero_documento) ILIKE CONCAT('%', w.word, '%')  -- Filtrado por número de documento
)
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
