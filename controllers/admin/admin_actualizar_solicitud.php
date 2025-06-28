<?php
session_start();
require 'database.php'; // Ruta correcta

// ---- Verificación de Rol de Administrador ----
define('ADMIN_ROLE_ID', 3); // ID Rol Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}
// ---- Fin Verificación ----

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Datos inválidos para actualizar.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitud_id'], $_POST['estado'])) {

    $solicitud_id = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);
    $nuevo_estado = htmlspecialchars(trim($_POST['estado']));
    $respuesta_admin = isset($_POST['respuesta']) ? trim($_POST['respuesta']) : ''; // Respuesta puede ser vacía

    $estados_permitidos = ['Pendiente', 'Aprobada', 'Rechazada', 'Procesada', 'Completada'];
    if (!$solicitud_id || $solicitud_id <= 0 || !in_array($nuevo_estado, $estados_permitidos)) {
        $response['message'] = 'ID de solicitud o estado proporcionado no válido.';
        echo json_encode($response);
        exit();
    }
    if ($nuevo_estado === 'Rechazada' && empty($respuesta_admin)) {
         $response['message'] = 'Se requiere una respuesta/motivo al rechazar la solicitud.';
         echo json_encode($response);
         exit();
    }

    try {
        $sql = "UPDATE solicitudes SET estado = ?, respuesta_admin = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) throw new Exception("Error al preparar: " . $conn->error);

        $stmt->bind_param("ssi", $nuevo_estado, $respuesta_admin, $solicitud_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Solicitud actualizada correctamente.';
            // Aquí podrías insertar una notificación para el familiar en la tabla 'notificaciones'
            // Ejemplo: INSERT INTO notificaciones (usuario_id, mensaje, tipo, relacion_id) VALUES (?, ?, 'solicitud', ?);
            // Donde usuario_id es el ID del familiar (necesitarías obtenerlo de la solicitud)
        } else {
            throw new Exception("Error al ejecutar: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        $response['message'] = 'Error interno del servidor al actualizar.';
        error_log("Error en admin_actualizar_solicitud.php: " . $e->getMessage());
        if (isset($stmt) && $stmt) $stmt->close();
        if (isset($conn) && $conn) $conn->close();
    }
}

echo json_encode($response);
?>