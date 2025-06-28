<?php
require 'database.php';
session_start();

header('Content-Type: application/json');
$response = ['error' => null, 'pacientes' => []];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

$familiar_id = $_SESSION['user_id'];
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

try {
    $sql = "SELECT u.id, u.nombres, u.apellidos, u.tipo_documento, u.documento, u.fecha_registro
            FROM usuarios u
            INNER JOIN familiares_pacientes fp ON u.id = fp.paciente_id
            WHERE fp.familiar_id = ?";

    $params = [$familiar_id];
    $types = "i";

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
        // Formatear fecha_registro
        $fecha_obj = new DateTime($row['fecha_registro']);
        $row['fecha_ingreso_formateada'] = $fecha_obj->format('d/m/Y H:i:s');
        $response['pacientes'][] = $row;
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en obtener_pacientes_familiares.php: " . $e->getMessage());
    $response['error'] = 'Ocurrió un error al obtener los datos de los familiares.';
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn) $conn->close();
}

echo json_encode($response);
?>