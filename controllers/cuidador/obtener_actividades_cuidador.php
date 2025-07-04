<?php
require 'database.php'; 
header('Content-Type: application/json');

session_start();
$cuidador_id = 1;

$actividades = [];
$error = null;

try {
    $sql = "SELECT a.descripcion, p.nombres AS paciente_nombre, p.apellidos AS paciente_apellido
            FROM actividades a
            LEFT JOIN pacientes p ON a.paciente_id = p.id
            WHERE a.cuidador_id = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cuidador_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['paciente_nombre'] = trim($row['paciente_nombre'] . ' ' . $row['paciente_apellido']);
            $actividades[] = $row;
        }
        $result->free();
    } else {
        $error = "Error al obtener actividades: " . $conn->error;
    }
    $stmt->close();
} catch (Exception $e) {
    $error = "Error general: " . $e->getMessage();
} finally {
    if ($conn) {
        $conn->close();
    }

    if ($error) {
        echo json_encode(['error' => $error]);
    } else {
        echo json_encode($actividades);
    }
}
?>