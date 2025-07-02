<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../../models/clases/usuario.php');

// Verificar que el usuario sea un familiar
if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Familiar') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$id_familiar = $_SESSION['id_usuario'];
$busqueda = $_GET['busqueda'] ?? '';

try {
    $familiar = new familiar();
    // Llamar al método del modelo para consultar pacientes
    $resultados = $familiar->consultarPacientesFamiliar($id_familiar, $busqueda);
    echo json_encode($resultados);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar los pacientes: ' . $e->getMessage()]);
}
