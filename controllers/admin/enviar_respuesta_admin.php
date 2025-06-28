<?php
require '../database.php'; // **RUTA CORRECTA ASUMIENDO database.php ESTÁ EN LA CARPETA PRINCIPAL gericare**
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['solicitud_id']) && isset($_POST['respuesta'])) {
        $solicitud_id = filter_var($_POST['solicitud_id'], FILTER_SANITIZE_NUMBER_INT);
        $respuesta = htmlspecialchars($_POST['respuesta']);

        if ($solicitud_id) {
            try {
                $sql = "UPDATE solicitudes SET respuesta_admin = ?, estado = 'respondida' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("si", $respuesta, $solicitud_id);
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            echo "success";
                        } else {
                            echo "error_update";
                        }
                    } else {
                        echo "error_execute";
                    }
                    $stmt->close();
                    $conn->close();
                } else {
                    echo "error_prepare";
                }
            } catch (Exception $e) {
                error_log("Error al enviar respuesta del admin: " . $e->getMessage());
                echo "error_exception";
            }
        } else {
            echo "invalid_solicitud_id";
        }
    } else {
        echo "missing_parameters";
    }
} else {
    echo "invalid_method";
}
?>