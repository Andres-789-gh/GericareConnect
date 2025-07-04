<?php
session_start();
header('Content-Type: application/json');

// Incluir el modelo de usuario
require_once(__DIR__ . '/../../models/clases/usuario.php');

// Seguridad: Verificar que el usuario sea un cuidador
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Cuidador') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$id_cuidador = $_SESSION['id_usuario'];
$busqueda = $_GET['busqueda'] ?? '';

try {
    // Se crea una instancia de la clase específica 'cuidador'
    $cuidador = new cuidador();
    
    // Se llama al método del modelo para consultar sus pacientes asignados
    $resultados = $cuidador->consultarPacientesAsignados($id_cuidador, $busqueda);

    // Se devuelven los resultados en formato JSON
    echo json_encode($resultados);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar los pacientes asignados: ' . $e->getMessage()]);
}
