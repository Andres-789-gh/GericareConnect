<?php
session_start();
require 'database.php';

header('Content-Type: application/json');
$response = ['error' => null, 'paciente' => null, 'actividades' => []];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}
 // Podrías añadir verificación de rol cuidador aquí si es necesario
 /*
 define('CUIDADOR_ROLE_ID', 2);
 if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != CUIDADOR_ROLE_ID) {
     $response['error'] = 'Acceso no autorizado.';
     echo json_encode($response);
     exit();
 }
 */

if (!isset($_GET['paciente_id'])) {
    $response['error'] = 'ID de paciente no proporcionado.';
    echo json_encode($response);
    exit();
}

$paciente_id = filter_input(INPUT_GET, 'paciente_id', FILTER_VALIDATE_INT);

if ($paciente_id === false || $paciente_id <= 0) {
    $response['error'] = 'ID de paciente no válido.';
    echo json_encode($response);
    exit();
}

try {
    // Obtener detalles del paciente
    $stmt_paciente = $conn->prepare("SELECT id, nombres, apellidos, tipo_documento, documento FROM usuarios WHERE id = ? AND rol_id = (SELECT id FROM roles WHERE nombre = 'Paciente')");
    if ($stmt_paciente === false) throw new Exception("Error preparando consulta paciente: " . $conn->error);
    $stmt_paciente->bind_param("i", $paciente_id);
    if (!$stmt_paciente->execute()) throw new Exception("Error ejecutando consulta paciente: " . $stmt_paciente->error);
    $result_paciente = $stmt_paciente->get_result();
    if ($result_paciente->num_rows > 0) {
        $response['paciente'] = $result_paciente->fetch_assoc();
    }
    $stmt_paciente->close();

    // Obtener actividades (SIN hora_programada)
    // *** CAMBIO AQUÍ: Se quita hora_programada de la consulta ***
    $stmt_actividades = $conn->prepare("SELECT id, descripcion, fecha_programada, estado FROM actividades WHERE paciente_id = ? ORDER BY fecha_programada");
     if ($stmt_actividades === false) throw new Exception("Error preparando consulta actividades: " . $conn->error);
    $stmt_actividades->bind_param("i", $paciente_id);
     if (!$stmt_actividades->execute()) throw new Exception("Error ejecutando consulta actividades: " . $stmt_actividades->error);
    $result_actividades = $stmt_actividades->get_result();

    while ($row = $result_actividades->fetch_assoc()) {
        // Formatear fecha si existe
        if (!empty($row['fecha_programada'])) {
             try {
                 $fecha_obj = new DateTime($row['fecha_programada']);
                 $row['fecha_programada_f'] = $fecha_obj->format('d/m/Y');
             } catch (Exception $dateEx) {
                 $row['fecha_programada_f'] = 'Fecha inválida'; // Manejo de fecha mala
             }
        } else {
             $row['fecha_programada_f'] = null; // Sin fecha
        }
         // Ya no se procesa la hora
        $response['actividades'][] = $row;
    }
// Para seleccionar la hora
$sql = "SELECT descripcion, fecha_programada, hora_programada, estado FROM tu_tabla_de_actividades WHERE paciente_id = ?";

// Para insertar una nueva actividad (ejemplo)
$sql_insert = "INSERT INTO tu_tabla_de_actividades (paciente_id, descripcion, fecha_programada, hora_programada, estado) VALUES (?, ?, ?, ?, ?)";
} catch (Exception $e) {
    $response['error'] = 'Error al obtener los detalles: ' . $e->getMessage();
    error_log("Error en obtener_detalle_paciente_cuidador.php: " . $e->getMessage());
} finally {
     if (isset($conn)) $conn->close();
}

echo json_encode($response);
?>