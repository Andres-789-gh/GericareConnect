<?php
require 'database.php';
session_start();

header('Content-Type: application/json');
$response = ['error' => null, 'solicitudes' => []];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

$usuario_id = $_SESSION['user_id'];
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : ''; // Añadido por si quieres buscar en el historial

try {
    // Seleccionamos todos los campos necesarios, incluyendo la respuesta y el estado
    $sql = "SELECT id, tipo_solicitud, descripcion, estado, respuesta_admin, paciente_id_relacionado, fecha_creacion
            FROM solicitudes
            WHERE usuario_id = ?";

    $params = [$usuario_id];
    $types = "i";

    if (!empty($buscar)) {
        $sql .= " AND (tipo_solicitud LIKE ? OR descripcion LIKE ? OR estado LIKE ?)";
        $buscarParam = "%" . $buscar . "%";
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $params[] = $buscarParam;
        $types .= "sss";
    }

    // Ordenar por fecha, las más recientes primero
    $sql .= " ORDER BY fecha_creacion DESC";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
         throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result === false) {
         throw new Exception("Error al obtener resultados: " . $stmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        // Formatear fecha para mejor visualización
        $fecha_obj = new DateTime($row['fecha_creacion']);
        $row['fecha_formateada'] = $fecha_obj->format('d/m/Y H:i');
        // Asegurarse que la respuesta admin sea null si está vacía en la BD
        $row['respuesta_admin'] = empty($row['respuesta_admin']) ? null : $row['respuesta_admin'];
        $response['solicitudes'][] = $row;
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en obtener_historial_solicitudes.php: " . $e->getMessage());
    $response['error'] = 'Ocurrió un error al obtener el historial de solicitudes.';
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn) $conn->close();
}

echo json_encode($response);
?>