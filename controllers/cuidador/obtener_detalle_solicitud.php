<?php
require '../database.php'; // **RUTA AJUSTADA**
session_start();

if (isset($_GET['id'])) {
    $solicitud_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($solicitud_id) {
        try {
            $sql = "SELECT s.id, s.tipo_solicitud, s.descripcion, s.estado, DATE_FORMAT(s.fecha_creacion, '%d/%m/%Y %H:%i') AS fecha_creacion, s.respuesta_admin,
                           u.nombres AS nombre_remitente, u.email AS email_remitente
                    FROM solicitudes s
                    JOIN usuarios u ON s.usuario_id = u.id
                    WHERE s.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $solicitud_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $solicitud = $result->fetch_assoc();
                header('Content-Type: application/json');
                echo json_encode($solicitud);
            } else {
                header('Content-Type: application/json');
                echo json_encode(null);
            }
            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            error_log("Error al obtener detalle de solicitud: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener el detalle de la solicitud.']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'ID de solicitud no válido.']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Se requiere el ID de la solicitud.']);
}
?>