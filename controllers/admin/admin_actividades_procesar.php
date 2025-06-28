<?php
require 'database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $paciente_id = isset($_POST['paciente_id']) && !empty($_POST['paciente_id']) ? htmlspecialchars(trim($_POST['paciente_id'])) : null;

    if (empty($descripcion)) {
        $response['error'] = 'La descripción de la tarea es obligatoria.';
    } else {
        $stmt = $conn->prepare("INSERT INTO actividades (descripcion, paciente_id) VALUES (?, ?)");
        $stmt->bind_param("si", $descripcion, $paciente_id);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Error al guardar la tarea: ' . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $response['error'] = 'Método de solicitud no válido.';
}

echo json_encode($response);
?>