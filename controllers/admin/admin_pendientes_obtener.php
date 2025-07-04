<?php
// Poner estas líneas AL PRINCIPIO de TODO
error_reporting(0);
ini_set('display_errors', 0);

session_start();

header('Content-Type: application/json'); // ANTES de cualquier salida

require 'database.php'; // Ruta correcta

$response = ['error' => null, 'solicitudes' => []]; // Respuesta base

try {
    // ---- Verificación de Rol de Administrador ----
    define('ADMIN_ROLE_ID', 3);
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
        throw new Exception('Acceso no autorizado.');
    }
    // ---- Fin Verificación ----

    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    if ($conn->connect_error) {
         throw new Exception("Error de Conexión BD: " . $conn->connect_error);
    }

    // Usar LEFT JOIN para incluir solicitudes incluso si el usuario o paciente relacionado fue borrado
    $sql = "SELECT s.id, s.usuario_id, s.tipo_solicitud, s.descripcion, s.estado,
                   s.datos_paciente_nuevo, s.paciente_id_relacionado, s.respuesta_admin,
                   s.fecha_creacion,
                   u.nombres AS familiar_nombres, u.apellidos AS familiar_apellidos, u.correo AS familiar_correo,
                   p.nombres AS paciente_rel_nombres, p.apellidos AS paciente_rel_apellidos
            FROM solicitudes s
            LEFT JOIN usuarios u ON s.usuario_id = u.id -- Usuario que envió
            LEFT JOIN usuarios p ON s.paciente_id_relacionado = p.id AND p.rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente') -- Paciente relacionado
            "; // Asume Rol Paciente ID=4

    $params = [];
    $types = "";
    $where_clauses = [];

    if (!empty($buscar)) {
        // Añadir condiciones de búsqueda
        $buscarParam = "%" . $buscar . "%";
        $where_clauses[] = "(s.tipo_solicitud LIKE ? OR s.descripcion LIKE ? OR s.estado LIKE ? OR u.nombres LIKE ? OR u.apellidos LIKE ? OR u.correo LIKE ? OR p.nombres LIKE ? OR p.apellidos LIKE ? OR s.id = ?)";
        $params = array_fill(0, 8, $buscarParam);
        $params[] = $buscar;
        $types = "ssssssssi";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(' AND ', $where_clauses); // Unir condiciones si hay más
    }

    $sql .= " ORDER BY s.fecha_creacion DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) throw new Exception("Error prepare(): " . $conn->error);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) throw new Exception("Error execute(): " . $stmt->error);

    $result = $stmt->get_result();
    if ($result === false) throw new Exception("Error get_result(): " . $stmt->error);

    $count = 0; // Contador para log
    while ($row = $result->fetch_assoc()) {
        $count++;
        // Procesar datos (igual que antes)
        $fecha_obj = new DateTime($row['fecha_creacion']);
        $row['fecha_formateada'] = $fecha_obj->format('d/m/Y H:i');
        $row['respuesta_admin'] = $row['respuesta_admin'] ?? null;
        $row['datos_paciente_nuevo'] = $row['datos_paciente_nuevo'] ? json_decode($row['datos_paciente_nuevo'], true) : null;
        $row['paciente_relacionado_nombre_completo'] = null;
        if ($row['paciente_id_relacionado'] && $row['paciente_rel_nombres']) {
            $row['paciente_relacionado_nombre_completo'] = trim($row['paciente_rel_nombres'] . ' ' . $row['paciente_rel_apellidos']);
        }
        unset($row['paciente_rel_nombres'], $row['paciente_rel_apellidos']);
        $response['solicitudes'][] = $row;
    }
    $stmt->close();
    $conn->close();

     error_log("[admin_solicitudes_obtener] Se obtuvieron {$count} solicitudes."); // Log cantidad

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log("Error en admin_solicitudes_obtener.php: " . $e->getMessage());
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn && $conn->ping()) $conn->close();
}

echo json_encode($response);
exit();
?>