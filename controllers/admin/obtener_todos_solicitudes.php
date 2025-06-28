<?php
require '../database.php'; // **RUTA AJUSTADA**
session_start();

$solicitudes = [];

try {
    $sql = "SELECT s.id, s.tipo_solicitud, s.descripcion, s.estado, DATE_FORMAT(s.fecha_creacion, '%d/%m/%Y %H:%i') AS fecha_creacion,
                   u.nombres AS nombre_remitente, u.email AS email_remitente
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.fecha_creacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($solicitudes);
} catch (Exception $e) {
    error_log("Error al obtener todas las solicitudes: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener las solicitudes.']);
}
?>