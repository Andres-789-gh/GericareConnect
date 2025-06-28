<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$solicitudes_pendientes = [];

$stmt = $conn->prepare("SELECT tipo_solicitud, descripcion, fecha_creacion FROM solicitudes WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $solicitudes_pendientes[] = $row;
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($solicitudes_pendientes);
?>