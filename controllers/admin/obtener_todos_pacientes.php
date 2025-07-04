<?php
require 'database.php';
session_start();

$pacientes = [];
$buscar = isset($_GET['buscar-paciente']) ? htmlspecialchars(trim($_GET['buscar-paciente'])) : '';

try {
    $sql = "SELECT id, nombres, apellidos
            FROM usuarios
            WHERE rol = 'Paciente'";

    $params = [];

    if (!empty($buscar)) {
        $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR documento LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params = [$buscarParam, $buscarParam, $buscarParam];
    }

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Error al preparar la consulta en obtener_todos_pacientes.php: " . $conn->error, 0);
        echo json_encode(['error' => 'Error interno del servidor al preparar la consulta.']);
        exit();
    }

    if (!empty($params)) {
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta en obtener_todos_pacientes.php: " . $stmt->error, 0);
        echo json_encode(['error' => 'Error interno del servidor al ejecutar la consulta.']);
        exit();
    }

    $result = $stmt->get_result();

    if ($result === false) {
        error_log("Error al obtener el resultado en obtener_todos_pacientes.php: " . $stmt->error, 0);
        echo json_encode(['error' => 'Error interno del servidor al obtener resultados.']);
        exit();
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($pacientes);

} catch (Exception $e) {
    error_log("Excepción en obtener_todos_pacientes.php: " . $e->getMessage(), 0);
    echo json_encode(['error' => 'Ocurrió un error inesperado en el servidor.']);
    exit();
}
?>