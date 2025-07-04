<?php
require 'database.php';
header('Content-Type: application/json');

$pacientes = [];

$stmt = $conn->prepare("SELECT id, nombres, apellidos FROM usuarios WHERE rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente') ORDER BY apellidos, nombres");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
    }
}
$stmt->close();
$conn->close();

echo json_encode($pacientes);
?>