<?php
session_start();
require 'database.php'; // Ruta correcta

// ---- Verificación de Rol de Administrador ----
define('ADMIN_ROLE_ID', 3);
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}
// ---- Fin Verificación ----

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['paciente_id'])) {
    $paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);

    if ($paciente_id === false || $paciente_id <= 0) {
        $response['message'] = 'ID de paciente no válido.';
    } else {
        // Obtener ID rol paciente para asegurar que solo borramos pacientes
        $stmt_rol = $conn->prepare("SELECT id FROM roles WHERE nombre = 'Paciente'");
        $stmt_rol->execute();
        $result_rol = $stmt_rol->get_result();
        $rol_paciente_id = $result_rol->fetch_assoc()['id'] ?? null;
        $stmt_rol->close();

         if($rol_paciente_id) {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND rol_id = ?");
            $stmt->bind_param("ii", $paciente_id, $rol_paciente_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Paciente eliminado correctamente.';
                    // Limpiar relaciones si no hay ON DELETE CASCADE configurado
                    // $conn->query("DELETE FROM familiares_pacientes WHERE paciente_id = $paciente_id");
                    // $conn->query("DELETE FROM actividades WHERE paciente_id = $paciente_id");
                } else {
                    $response['message'] = 'No se encontró el paciente especificado o ya había sido eliminado.';
                }
            } else {
                // Revisar si hay restricciones de FK que impiden borrar
                if ($conn->errno == 1451) { // Código de error para restricción FK
                     $response['message'] = 'Error: No se puede eliminar el paciente porque tiene datos asociados (ej. solicitudes, actividades). Elimine primero los datos asociados.';
                } else {
                    $response['message'] = 'Error al ejecutar la eliminación: (' . $stmt->errno . ') ' . $stmt->error;
                }
                error_log("Error eliminando paciente ID $paciente_id: " . $stmt->error);
            }
            $stmt->close();
         } else {
              $response['message'] = 'Error interno: No se encontró el rol de Paciente.';
         }
        $conn->close();
    }
}

echo json_encode($response);
?>