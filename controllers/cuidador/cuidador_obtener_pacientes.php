<?php
session_start();
require 'database.php'; // Asegúrate que la ruta sea correcta

header('Content-Type: application/json');
$response = ['error' => null, 'pacientes' => []];

// --- Verificación de Sesión y Rol de Cuidador ---
define('CUIDADOR_ROLE_ID', 2); // ID Rol Cuidador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id'])) {
    $response['error'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}
if ($_SESSION['user_rol_id'] != CUIDADOR_ROLE_ID) {
     $response['error'] = 'Acceso no autorizado. Se requiere rol de Cuidador.';
     echo json_encode($response);
     exit();
}
$cuidador_id = $_SESSION['user_id'];
// --- Fin Verificación ---

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

try {
    // Consulta para obtener los IDs únicos de pacientes asignados a actividades del cuidador
    // Luego, se obtienen los detalles de esos pacientes
    $sql = "SELECT DISTINCT u.id, u.nombres, u.apellidos, u.tipo_documento, u.documento
            FROM usuarios u
            JOIN actividades a ON u.id = a.paciente_id
            WHERE a.cuidador_id = ?";

    $params = [$cuidador_id];
    $types = "i";

    // Añadir filtro de búsqueda si existe
    if (!empty($buscar)) {
        $sql .= " AND (u.nombres LIKE ? OR u.apellidos LIKE ? OR u.documento LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $types .= "sss";
    }

    $sql .= " ORDER BY u.apellidos, u.nombres";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta de pacientes: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta de pacientes: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        throw new Exception("Error al obtener resultados de pacientes: " . $stmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        $response['pacientes'][] = $row;
    }

    $stmt->close();

} catch (Exception $e) {
    $response['error'] = 'Ocurrió un error al obtener los pacientes asignados: ' . $e->getMessage();
    error_log("Error en cuidador_obtener_pacientes.php: " . $e->getMessage());
    if (isset($stmt) && $stmt) $stmt->close();
} finally {
    if (isset($conn) && $conn) $conn->close();
}

echo json_encode($response);
?>