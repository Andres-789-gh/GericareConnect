<?php
session_start();
header('Content-Type: application/json');

// Incluimos la clase que sabe cómo desactivar usuarios.
require_once(__DIR__ . '/../../models/clases/usuario.php');
require_once(__DIR__ . '/../../models/clases/pacientes.php');

// Seguridad: solo un admin puede desactivar.
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

$id_entidad = $_POST['id'] ?? 0;
$tipo_entidad = $_POST['tipo'] ?? '';
$id_admin_actual = $_SESSION['id_usuario'] ?? 0;

if ($id_entidad <= 0 || empty($tipo_entidad)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
    exit();
}

try {
    // Creamos un objeto administrador para usar sus métodos.
    $admin = new administrador();
    $paciente = new Paciente();
    
    // Decidimos qué método llamar basado en lo que se quiere desactivar.
    if ($tipo_entidad === 'Usuario') {
        $admin->desactivarUsuario($id_entidad, $id_admin_actual);
        $message = 'Usuario desactivado correctamente.';
    } elseif ($tipo_entidad === 'Paciente') {
        $paciente->desactivar($id_entidad);
        $message = 'Paciente desactivado correctamente.';
    } else {
        throw new Exception('Tipo de entidad no reconocido.');
    }

    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    http_response_code(500);
    // Extraemos solo el mensaje de error de la base de datos para mostrarlo de forma amigable.
    $errorMessage = explode(":", $e->getMessage());
    echo json_encode(['success' => false, 'message' => trim(end($errorMessage))]);
}
?>
