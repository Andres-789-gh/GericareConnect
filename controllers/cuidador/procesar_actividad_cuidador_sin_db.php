<?php
header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $paciente_id = isset($_POST['paciente_id']) && !empty($_POST['paciente_id']) ? htmlspecialchars(trim($_POST['paciente_id'])) : null;

    $response['success'] = true;
    echo json_encode($response);
    exit();
} else {
    $response['error'] = 'Método de solicitud no válido.';
    echo json_encode($response);
    exit();
}
?>