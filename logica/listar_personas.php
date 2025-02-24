<?php
require 'bd.php'; // Asegúrate de que este archivo define correctamente $conectar

// Verificar si las variables de DataTables están definidas antes de usarlas
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Manejo del ordenamiento
$columnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$columnOrder = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id';
$orderDir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';

// Definir las columnas permitidas en la consulta
$columnas = ['id', 'numero_documento', 'nombre', 'condicion', 'telefono', 'deleted_at'];
if (!in_array($columnOrder, $columnas)) {
    $columnOrder = 'id';
}

// Construcción de la consulta SQL base
$sqlBase = "SELECT id, numero_documento, 
                   CASE 
                       WHEN nombres IS NOT NULL AND apellidos IS NOT NULL THEN CONCAT(nombres, ' ', apellidos)
                       WHEN razon_social IS NOT NULL THEN razon_social
                       ELSE '-'
                   END AS nombre, 
                   COALESCE(condicion, '-') AS condicion, 
                   COALESCE(telefonomovil, COALESCE(telefonofijo, '-')) AS telefono, 
                   deleted_at 
            FROM persona
            
            --where deleted_at is null   
            ";
// Aplicar búsqueda si hay un valor de búsqueda
$sqlFiltro = "";
$params = [];
if (!empty($searchValue)) {
    $sqlFiltro = " WHERE (numero_documento ILIKE ? OR 
                          nombres ILIKE ? OR 
                          apellidos ILIKE ? OR 
                          razon_social ILIKE ? OR 
                          condicion ILIKE ? OR 
                          telefonomovil ILIKE ? OR 
                          telefonofijo ILIKE ?)";
    $params = array_fill(0, 7, "%$searchValue%");
}

// Obtener el total de registros sin filtrar
$sqlTotal = "SELECT COUNT(*) FROM persona";
$totalRecords = $conectar->query($sqlTotal)->fetchColumn();

// Obtener el total de registros filtrados
$sqlFilteredTotal = "SELECT COUNT(*) FROM persona " . $sqlFiltro;
$stmtFilteredTotal = $conectar->prepare($sqlFilteredTotal);
$stmtFilteredTotal->execute($params);
$totalFiltered = $stmtFilteredTotal->fetchColumn();

// Agregar orden, paginación y ejecutar la consulta final
$sqlFinal = $sqlBase . $sqlFiltro . " ORDER BY $columnOrder $orderDir LIMIT $length OFFSET $start";
$stmtFinal = $conectar->prepare($sqlFinal);
$stmtFinal->execute($params);
$personas = $stmtFinal->fetchAll(PDO::FETCH_ASSOC);

// Agregar botones de acción en cada fila
foreach ($personas as &$persona) {
    $id = $persona['id'];
    $persona['acciones'] = '
        <a class="btn btn-warning btn-round ml-2" onclick=\'fn_editar_usuario('.json_encode($persona).')\'>
            <i class="fa fa-edit"></i>
        </a>
        '.($persona["deleted_at"] == null ? 
        '<a class="btn btn-dark btn-round ml-2" onclick="fn_bloquear_usuario('.$id.')"><i class="fa fa-lock"></i></a>' :
        '<a class="btn btn-secondary btn-round ml-2" onclick="fn_desbloquear_usuario('.$id.')"><i class="fa fa-unlock"></i></a>').'
      ';
}

// Formato de salida compatible con DataTables
$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $personas
];

echo json_encode($response);
?>
