<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo "Error: Usuario no autenticado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_solicitud = $_POST['tipo_solicitud'];
    $motivo = $_POST['motivo'];
    $usuario_id = $_SESSION['user_id'];

    if (empty($tipo_solicitud) || empty($motivo)) {
        echo "Error: Por favor, complete todos los campos.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO solicitudes (usuario_id, tipo_solicitud, descripcion, fecha_creacion) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $usuario_id, $tipo_solicitud, $motivo);

    if ($stmt->execute()) {
        echo "Solicitud enviada correctamente.";
        exit();
    } else {
        echo "Error al guardar la solicitud: " . $stmt->error;
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Error: Acceso no permitido.";
    exit();
}
?>