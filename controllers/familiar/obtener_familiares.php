<?php
require 'database.php';
session_start();

$familiares = [];
$buscar = isset($_GET['buscar-familiar']) ? htmlspecialchars(trim($_GET['buscar-familiar'])) : '';

try {
    $sql = "SELECT id, nombres, apellidos
            FROM usuarios
            WHERE rol = 'Familiar'";

    $params = [];

    if (!empty($buscar)) {
        $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR documento LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params = [$buscarParam, $buscarParam, $buscarParam];
    }

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Error al preparar la consulta en obtener_familiares.php: " . $conn->error);
        echo json_encode(['error' => 'Error interno del servidor al preparar la consulta.']);
        exit();
    }

    if (!empty($params)) {
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta en obtener_familiares.php: " . $stmt->error);
        echo json_encode(['error' => 'Error interno del servidor al ejecutar la consulta.']);
        exit();
    }

    $result = $stmt->get_result();

    if ($result === false) {
        error_log("Error al obtener el resultado en obtener_familiares.php: " . $stmt->error);
        echo json_encode(['error' => 'Error interno del servidor al obtener resultados.']);
        exit();
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $familiares[] = $row;
        }
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($familiares);

} catch (Exception $e) {
    error_log("Excepción en obtener_familiares.php: " . $e->getMessage());
    echo json_encode(['error' => 'Ocurrió un error inesperado en el servidor.']);
    exit();
}
?>