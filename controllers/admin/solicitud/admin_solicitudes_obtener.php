<?php
session_start();
require 'database.php'; // Ruta correcta

// ---- Verificación de Rol de Administrador ----
define('ADMIN_ROLE_ID', 3); // Asegúrate que '3' sea el ID correcto
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado.', 'solicitudes' => []]);
    exit();
}
// ---- Fin Verificación ----

header('Content-Type: application/json');
$response = ['error' => null, 'solicitudes' => []];
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

try {
    // Consulta optimizada para obtener datos necesarios
    $sql = "SELECT s.id, s.usuario_id, s.tipo_solicitud, s.descripcion, s.estado,
                   s.datos_paciente_nuevo, s.paciente_id_relacionado, s.respuesta_admin,
                   s.fecha_creacion,
                   u.nombres AS familiar_nombres, u.apellidos AS familiar_apellidos, u.correo AS familiar_correo,
                   p.nombres AS paciente_rel_nombres, p.apellidos AS paciente_rel_apellidos
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            LEFT JOIN usuarios p ON s.paciente_id_relacionado = p.id AND p.rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente') -- Rol Paciente ID=4 (ajustar si es diferente)
            ";

    $params = [];
    $types = "";

    // Cláusula WHERE para búsqueda
    if (!empty($buscar)) {
        $sql .= " WHERE (s.tipo_solicitud LIKE ? OR s.descripcion LIKE ? OR s.estado LIKE ? OR u.nombres LIKE ? OR u.apellidos LIKE ? OR u.correo LIKE ? OR p.nombres LIKE ? OR p.apellidos LIKE ? OR s.id = ?)"; // Añadido buscar por ID
        $buscarParam = "%" . $buscar . "%";
        $params = array_fill(0, 8, $buscarParam); // 8 placeholders de string
        $params[] = $buscar; // 1 placeholder de entero (para ID)
        $types = "ssssssssi"; // 8 strings, 1 integer
    }

    $sql .= " ORDER BY s.fecha_creacion DESC"; // Ordenar por más reciente

    $stmt = $conn->prepare($sql);
    if ($stmt === false) throw new Exception("Error prepare(): " . $conn->error);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) throw new Exception("Error execute(): " . $stmt->error);

    $result = $stmt->get_result();
    if ($result === false) throw new Exception("Error get_result(): " . $stmt->error);

    while ($row = $result->fetch_assoc()) {
        // Procesar datos antes de añadir a la respuesta
        $fecha_obj = new DateTime($row['fecha_creacion']);
        $row['fecha_formateada'] = $fecha_obj->format('d/m/Y H:i');
        $row['respuesta_admin'] = $row['respuesta_admin'] ?? null; // Asegurar null si está vacío
        $row['datos_paciente_nuevo'] = $row['datos_paciente_nuevo'] ? json_decode($row['datos_paciente_nuevo'], true) : null;
        $row['paciente_relacionado_nombre_completo'] = null;
        if ($row['paciente_id_relacionado'] && $row['paciente_rel_nombres']) {
            $row['paciente_relacionado_nombre_completo'] = trim($row['paciente_rel_nombres'] . ' ' . $row['paciente_rel_apellidos']);
        }
        unset($row['paciente_rel_nombres'], $row['paciente_rel_apellidos']); // Limpiar campos redundantes
        $response['solicitudes'][] = $row;
    }
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en admin_solicitudes_obtener.php: " . $e->getMessage());
    $response['error'] = 'Ocurrió un error al obtener las solicitudes.';
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn) $conn->close();
}

echo json_encode($response);
?>