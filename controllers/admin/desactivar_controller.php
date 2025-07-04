<?php
session_start();
header('Content-Type: application/json');

require_once(__DIR__ . '/../../models/clases/usuario.php');

// Verificación de seguridad
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

// Recibir datos por POST
$id_entidad = $_POST['id'] ?? 0;
$tipo_entidad = $_POST['tipo'] ?? '';
$id_admin_actual = $_SESSION['id_usuario'] ?? 0;

if ($id_entidad <= 0 || empty($tipo_entidad)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
    exit();
}

try {
    $admin = new administrador();
    
    // Decidir que metodo llamar basado en lo que se desee eliminar
    if ($tipo_entidad === 'Usuario') {
        $admin->desactivarUsuario($id_entidad, $id_admin_actual);
        $message = 'Usuario eliminado correctamente.';
    } elseif ($tipo_entidad === 'Paciente') {
        $admin->desactivarPaciente($id_entidad);
        $message = 'Paciente eliminado correctamente.';
    } else {
        throw new Exception('Tipo de entidad no reconocido.');
    }

    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    http_response_code(500);
    // Extraer solo el mensaje de error de la base de datos para mostrarlo
    $errorMessage = explode(":", $e->getMessage());
    echo json_encode(['success' => false, 'message' => trim(end($errorMessage))]);
}
