<?php
session_start();
require 'database.php';


define('ADMIN_ROLE_ID', 3);
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitud_id'])) {
    $solicitud_id = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);

    if ($solicitud_id === false || $solicitud_id <= 0) {
        $response['message'] = 'ID de solicitud no válido.';
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM solicitudes WHERE id = ?");
            if($stmt === false) throw new Exception("Error al preparar la consulta: ".$conn->error);

            $stmt->bind_param("i", $solicitud_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Solicitud eliminada correctamente.';
                } else {
                    $response['message'] = 'No se encontró la solicitud especificada o ya había sido eliminada.';
                }
            } else {
                 throw new Exception('Error al ejecutar la eliminación: ' . $stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Error interno del servidor al eliminar la solicitud.';
             error_log("Error eliminando solicitud ID $solicitud_id: " . $e->getMessage());
             if(isset($stmt) && $stmt) $stmt->close();
        } finally {
             if(isset($conn) && $conn) $conn->close();
        }
    }
}

echo json_encode($response);
?>