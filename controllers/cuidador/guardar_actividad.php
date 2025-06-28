<?php
session_start();
require 'database.php'; // Asegúrate que la ruta sea correcta

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'error' => null]; // Respuesta inicial

// --- Verificación de Autenticación y Rol (similar al otro script) ---
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Usuario no autenticado.';
    $response['message'] = 'Debes iniciar sesión para realizar esta acción.';
    echo json_encode($response);
    exit();
}

// --- Verificación de Rol (Opcional, descomentar si es necesario) ---
/*
define('CUIDADOR_ROLE_ID', 2); // Asume que 2 es el ID del rol Cuidador
if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != CUIDADOR_ROLE_ID) {
    $response['error'] = 'Acceso no autorizado.';
    $response['message'] = 'No tienes permisos para agregar actividades.';
    echo json_encode($response);
    exit();
}
*/

// --- Procesamiento del Formulario (Solo si es método POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Obtener y Validar Datos del POST ---
    // Asegúrate que los nombres 'paciente_id', 'descripcion', etc., coincidan con los atributos 'name' de tu formulario HTML
    $paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $fecha_programada = filter_input(INPUT_POST, 'fecha_programada', FILTER_SANITIZE_STRING); // Recibida como string
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

    // ** NO SE INTENTA OBTENER 'hora_programada' **

    // Validaciones básicas
    if ($paciente_id === false || $paciente_id <= 0) {
        $response['error'] = 'ID de paciente no válido.';
        $response['message'] = 'El paciente seleccionado no es correcto.';
    } elseif (empty($descripcion)) {
        $response['error'] = 'Descripción requerida.';
        $response['message'] = 'Debes ingresar una descripción para la actividad.';
    } elseif (empty($fecha_programada)) {
        $response['error'] = 'Fecha requerida.';
        $response['message'] = 'Debes seleccionar una fecha para la actividad.';
    } elseif (empty($estado)) {
        $response['error'] = 'Estado requerido.';
        $response['message'] = 'Debes seleccionar un estado para la actividad.';
    } else {
        // Validar formato de fecha (ej: YYYY-MM-DD esperado por MySQL DATE)
        // El formato 'd/m/Y' del script anterior es para *mostrar*, no necesariamente para guardar.
        // Ajusta esto según el formato que envíe tu campo de fecha y el que espere tu BD.
        try {
             // Intenta convertir a formato YYYY-MM-DD si viene como DD/MM/YYYY
             $fecha_obj = DateTime::createFromFormat('d/m/Y', $fecha_programada); // Asume formato de entrada DD/MM/YYYY
             if ($fecha_obj === false) {
                 // Intentar si ya viene en formato YYYY-MM-DD
                 $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_programada);
             }

             if ($fecha_obj === false) {
                throw new Exception("Formato de fecha no válido. Se esperaba DD/MM/YYYY o YYYY-MM-DD.");
             }
             $fecha_para_db = $fecha_obj->format('Y-m-d'); // Formato estándar para MySQL DATE

        } catch (Exception $e) {
            $response['error'] = 'Fecha inválida.';
            $response['message'] = $e->getMessage();
            echo json_encode($response);
            exit();
        }


        // --- Preparar y Ejecutar el INSERT ---
        // ** LA CONSULTA SQL NO INCLUYE hora_programada **
        $sql = "INSERT INTO actividades (paciente_id, descripcion, fecha_programada, estado) VALUES (?, ?, ?, ?)";

        try {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparando la consulta INSERT: " . $conn->error);
            }

            // Vincula los parámetros: i=integer, s=string, s=string(date), s=string
            $stmt->bind_param("isss", $paciente_id, $descripcion, $fecha_para_db, $estado);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Actividad guardada correctamente.';
            } else {
                throw new Exception("Error al ejecutar la consulta INSERT: " . $stmt->error);
            }
            $stmt->close();

        } catch (Exception $e) {
            $response['error'] = 'Error de base de datos.';
            $response['message'] = 'No se pudo guardar la actividad: ' . $e->getMessage();
            // Log del error para depuración interna
            error_log("Error en guardar_actividad.php: " . $e->getMessage());
        }
    }

} else {
    // Si no es POST
    $response['error'] = 'Método no permitido.';
    $response['message'] = 'Esta URL solo acepta solicitudes POST.';
}

// --- Cerrar conexión y enviar respuesta ---
if (isset($conn)) {
    $conn->close();
}
echo json_encode($response);
?>