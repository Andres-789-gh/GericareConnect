<?php
session_start();
require 'database.php';

// --- IDs de Roles Permitidos ---
define('ADMIN_ROLE_ID', 3);
define('CUIDADOR_ROLE_ID', 2); // ID Rol Cuidador
// --- Fin IDs ---

// --- Verificación de Roles Permitidos ---
// Verificar que el usuario esté logueado y tenga uno de los roles permitidos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) ||
    !in_array($_SESSION['user_rol_id'], [ADMIN_ROLE_ID, CUIDADOR_ROLE_ID])) { // Cambio aquí: permite Admin O Cuidador

    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado.', 'pacientes' => []]);
    exit();
}
// --- Fin Verificación ---

header('Content-Type: application/json');
// La estructura de respuesta ahora solo necesita error y pacientes para este caso
// Podríamos quitar 'conteo_solicitudes_pendientes' si no es relevante para el cuidador
$response = ['error' => null, 'pacientes' => []];
$buscar = isset($_GET['buscar-paciente']) ? trim($_GET['buscar-paciente']) : ''; // Usar el mismo parámetro que admin

try {
    // Obtener ID del rol 'Paciente'
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
            WHERE rol_id = ?"; // Seleccionar solo usuarios con rol de Paciente
    $params = [$rol_paciente_id];
    $types = "i";

    if (!empty($buscar)) {
        $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR documento LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $types .= "sss";
    }

    $sql .= " ORDER BY apellidos, nombres";

    $stmt_pacientes = $conn->prepare($sql);
    if ($stmt_pacientes === false) throw new Exception("Error preparando consulta de pacientes: " . $conn->error);

    $stmt_pacientes->bind_param($types, ...$params);

    if (!$stmt_pacientes->execute()) throw new Exception("Error ejecutando consulta de pacientes: " . $stmt_pacientes->error);

    $result_pacientes = $stmt_pacientes->get_result();
    if ($result_pacientes === false) throw new Exception("Error obteniendo resultados de pacientes: " . $stmt_pacientes->error);

    while ($row = $result_pacientes->fetch_assoc()) {
        $response['pacientes'][] = $row;
    }
    $stmt_pacientes->close();

    // Opcional: Podrías decidir si el conteo de solicitudes es relevante para el cuidador
    // Si no lo es, puedes comentar o eliminar esta parte.
    /*
    $sql_count = "SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'Pendiente'";
    $result_count = $conn->query($sql_count);
    if ($result_count) {
        $response['conteo_solicitudes_pendientes'] = $result_count->fetch_assoc()['total'] ?? 0;
    } else {
         error_log("Error al contar solicitudes pendientes: ".$conn->error);
    }
    */


} catch (Exception $e) {
    $response['error'] = 'Ocurrió un error al obtener los datos: ' . $e->getMessage();
    error_log("Error en admin_pacientes_obtener_lista.php (accedido por cuidador?): " . $e->getMessage());
    if (isset($stmt_pacientes) && $stmt_pacientes) $stmt_pacientes->close();
} finally {
     if (isset($conn) && $conn) $conn->close();
}

echo json_encode($response);
?>