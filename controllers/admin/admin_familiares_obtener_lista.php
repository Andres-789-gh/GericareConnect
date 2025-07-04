<?php
session_start();
require 'database.php'; // Ruta correcta

// ---- Verificación de Rol de Administrador ----
define('ADMIN_ROLE_ID', 3);
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    header('Content-Type: application/json');
    // Devolver un array vacío o un error JSON en lugar de redirigir
    echo json_encode([]); // Devuelve array vacío si no es admin
    exit();
}
// ---- Fin Verificación ----

header('Content-Type: application/json');
$pacientes = [];
$buscar = isset($_GET['buscar-paciente']) ? trim($_GET['buscar-paciente']) : '';

try {
    // Obtener ID del rol 'Paciente' para asegurar que solo listamos pacientes
    $stmt_rol = $conn->prepare("SELECT id FROM roles WHERE nombre = 'Paciente'");
    if ($stmt_rol === false) throw new Exception("Error preparando consulta de rol: " . $conn->error);
    $stmt_rol->execute();
    $result_rol = $stmt_rol->get_result();
    if ($result_rol->num_rows === 0) throw new Exception("Rol 'Paciente' no encontrado en la base de datos.");
    $rol_paciente_id = $result_rol->fetch_assoc()['id'];
    $stmt_rol->close();

    // Consulta principal para obtener pacientes
    $sql = "SELECT id, nombres, apellidos, tipo_documento, documento
            FROM usuarios
            WHERE rol_id = ?";
    $params = [$rol_paciente_id];
    $types = "i";

    if (!empty($buscar)) {
        $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR documento LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params[] = $buscarParam; // Añadir parámetro al array
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $types .= "sss"; // Añadir tipos de string
    }

    $sql .= " ORDER BY apellidos, nombres"; // Ordenar alfabéticamente

    $stmt_pacientes = $conn->prepare($sql);
    if ($stmt_pacientes === false) throw new Exception("Error preparando consulta de pacientes: " . $conn->error);

    // Vincular parámetros dinámicamente
    if (!empty($params)) {
        $stmt_pacientes->bind_param($types, ...$params);
    } else {
         $stmt_pacientes->bind_param($types, $rol_paciente_id); // Solo vincular rol_id si no hay búsqueda
    }


    if (!$stmt_pacientes->execute()) throw new Exception("Error ejecutando consulta de pacientes: " . $stmt_pacientes->error);

    $result_pacientes = $stmt_pacientes->get_result();
    if ($result_pacientes === false) throw new Exception("Error obteniendo resultados de pacientes: " . $stmt_pacientes->error);

    // Recoger resultados
    while ($row = $result_pacientes->fetch_assoc()) {
        $pacientes[] = $row;
    }

    $stmt_pacientes->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en admin_pacientes_obtener_lista.php: " . $e->getMessage());
    // Devolver array vacío en caso de error para que el JS no falle
    $pacientes = [];
    // Podrías añadir un campo de error al JSON si quieres manejarlo en el frontend
    // echo json_encode(['error' => $e->getMessage(), 'pacientes' => []]);
    // exit();
}

// Devolver siempre un JSON, aunque sea un array vacío
echo json_encode($pacientes);
?>