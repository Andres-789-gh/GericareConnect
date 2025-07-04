<?php
require 'database.php';
session_start();

$solicitudes = [];

if (isset($_SESSION['user_id'])) {
    $usuario_id = $_SESSION['user_id'];
    try {
        $sql = "SELECT s.id, s.tipo_solicitud, s.descripcion, s.estado, DATE_FORMAT(s.fecha_creacion, '%d/%m/%Y %H:%i') AS fecha_creacion
                FROM solicitudes s
                WHERE s.remitente_id = ? AND s.estado = 'pendiente'
                ORDER BY s.fecha_creacion DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
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
        error_log("Error al obtener solicitudes pendientes: " . $e->getMessage());
        echo json_encode(['error' => 'Error al obtener las solicitudes.']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>