<?php
// Archivo: gericare/cuidador_procesar_actividad.php

// Iniciar sesión para acceder a las variables de sesión
session_start();

// Incluir el archivo de conexión a la base de datos
require 'database.php'; // Asegúrate que la ruta sea correcta

// Establecer la cabecera para la respuesta JSON
header('Content-Type: application/json');

// Respuesta inicial por defecto
$response = ['success' => false, 'message' => 'Error desconocido al procesar la actividad.'];

// --- Verificación de Sesión y Rol ---
// Define el ID del rol esperado para un Cuidador (ajusta si es diferente en tu tabla 'roles')
define('CUIDADOR_ROLE_ID', 2);

// Verificar si el usuario está autenticado y tiene el rol correcto
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id'])) {
    $response['message'] = 'Usuario no autenticado. Por favor, inicie sesión.';
    echo json_encode($response);
    exit();
}
if ($_SESSION['user_rol_id'] != CUIDADOR_ROLE_ID) {
     $response['message'] = 'Acceso no autorizado. Se requiere rol de Cuidador.';
     echo json_encode($response);
     exit();
}
// Obtener el ID del cuidador desde la sesión
$cuidador_id = $_SESSION['user_id'];
// --- Fin Verificación ---

// --- Procesamiento del Formulario (Solo si es método POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Obtener y Validar Datos del POST ---
    // Obtener ID del paciente, asegurándose que sea un entero positivo
    $paciente_id = filter_input(INPUT_POST, 'paciente_id', FILTER_VALIDATE_INT);
    // Obtener descripción, eliminando espacios extra
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    // Obtener fecha, convertirla a NULL si está vacía
    $fecha_programada = isset($_POST['fecha_programada']) && !empty($_POST['fecha_programada']) ? $_POST['fecha_programada'] : null;
    // Obtener hora, convertirla a NULL si está vacía
    $hora_programada = isset($_POST['hora_programada']) && !empty($_POST['hora_programada']) ? $_POST['hora_programada'] : null;
    // Obtener estado, con 'Pendiente' como valor por defecto
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'Pendiente';

    // --- DEBUGGING: Registrar el ID de paciente recibido ---
    error_log("GeriCare DEBUG - Procesando actividad. Recibido paciente_id: " . print_r($paciente_id, true));

    // --- Validaciones Adicionales ---
    // Verificar que los campos obligatorios no estén vacíos y que el ID sea válido
    if (empty($descripcion) || $paciente_id === false || $paciente_id <= 0) {
        // --- DEBUGGING: Registrar fallo de validación ---
        error_log("GeriCare DEBUG - Validación fallida: paciente_id inválido o descripción vacía.");
        $response['message'] = 'Faltan datos requeridos (Paciente y Descripción).';
        echo json_encode($response);
        exit();
    }

    // Validación simple de hora si se proporciona (formato HH:MM o HH:MM:SS)
    if ($hora_programada !== null && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora_programada)) {
         $response['message'] = 'El formato de la hora proporcionada no es válido (HH:MM).';
         echo json_encode($response);
         exit();
    }
    // Validación simple de fecha si se proporciona (formato YYYY-MM-DD)
    if ($fecha_programada !== null) {
        $d = DateTime::createFromFormat('Y-m-d', $fecha_programada);
        if (!$d || $d->format('Y-m-d') !== $fecha_programada) {
             $response['message'] = 'El formato de la fecha proporcionada no es válido (YYYY-MM-DD).';
             echo json_encode($response);
             exit();
        }
    }

    // Validar el estado contra una lista permitida
    $estados_validos = ['Pendiente', 'En Progreso', 'Completada', 'Cancelada'];
    if (!in_array($estado, $estados_validos)) {
        $estado = 'Pendiente'; // Asignar estado por defecto si no es válido
    }

    // --- Interacción con la Base de Datos ---
    try {
        // Preparar la consulta SQL para insertar la actividad
        // Incluye la columna 'hora_programada'
        // Asume que la tabla 'actividades' tiene una columna 'cuidador_id'
        $sql = "INSERT INTO actividades (paciente_id, cuidador_id, descripcion, fecha_programada, hora_programada, estado, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        // Verificar si la preparación de la consulta falló
        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta INSERT: " . $conn->error);
        }

        // Vincular los parámetros a la consulta preparada
        // i: integer, s: string
        // Ajusta los tipos si 'cuidador_id' no existe o si fecha/hora no son strings en tu BD
        $stmt->bind_param("iissss",
            $paciente_id,
            $cuidador_id,
            $descripcion,
            $fecha_programada, // Se pasa como string o null
            $hora_programada,  // Se pasa como string o null
            $estado
        );

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Si la inserción fue exitosa
            $response['success'] = true;
            $response['message'] = 'Actividad registrada correctamente.';
        } else {
            // Si la ejecución falló (aquí es donde probablemente ocurre el error FK)
            // --- DEBUGGING: Registrar el error específico de MySQL ---
            error_log("GeriCare DEBUG - Error MySQL al insertar actividad: (" . $stmt->errno . ") " . $stmt->error);
            throw new Exception("Error al registrar la actividad en la base de datos.");
        }
        // Cerrar la sentencia preparada
        $stmt->close();

    } catch (Exception $e) {
        // Capturar cualquier excepción durante la interacción con la BD
        $response['message'] = 'Error interno del servidor: ' . $e->getMessage();
        // Registrar el error detallado en los logs del servidor
        error_log("Error en cuidador_procesar_actividad.php: " . $e->getMessage());
        // Asegurarse de cerrar el statement si aún está abierto
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
    } finally {
        // Asegurarse de cerrar la conexión a la BD
         if (isset($conn)) {
            $conn->close();
         }
    }

} else {
     // Si el método de la solicitud no es POST
     $response['message'] = 'Método de solicitud no permitido.';
}

// Enviar la respuesta final en formato JSON
echo json_encode($response);
?>