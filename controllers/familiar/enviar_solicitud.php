<?php
// Poner estas líneas AL PRINCIPIO de TODO
error_reporting(0);
ini_set('display_errors', 0);

session_start();

header('Content-Type: application/json'); // Establecer ANTES de cualquier salida

require 'database.php'; // Ruta correcta

$response = ['success' => false, 'message' => 'Error desconocido al enviar solicitud.']; // Mensaje por defecto

try {
    // 1. Verificar si existe el ID de usuario en la sesión
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Error: Sesión no iniciada o expirada. Por favor, inicie sesión.');
    }
    $usuario_id = $_SESSION['user_id'];

    // 2. Verificar si el ID de usuario de la sesión REALMENTE existe en la BD
    if ($conn->connect_error) {
         throw new Exception("Error de Conexión BD: " . $conn->connect_error);
    }
    $check_user_stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    if ($check_user_stmt === false) throw new Exception("Error preparando verificación (1): " . $conn->error);
    $check_user_stmt->bind_param("i", $usuario_id);
    if (!$check_user_stmt->execute()) throw new Exception("Error ejecutando verificación (1): " . $check_user_stmt->error);
    $result_check = $check_user_stmt->get_result();
    if ($result_check->num_rows === 0) {
        $check_user_stmt->close();
        session_unset(); session_destroy(); // Destruir sesión inválida
        error_log("Foreign key error evitado (enviar_solicitud): User ID $usuario_id de sesión NO existe en tabla usuarios.");
        throw new Exception('Error: Sesión inválida. Inicie sesión de nuevo.');
    }
    $check_user_stmt->close();
    // ---- Usuario de sesión válido ----


    // 3. Validar el método de la petición
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
         throw new Exception('Error: Método de solicitud no válido.');
    }

    // 4. Procesar los datos del formulario
    $tipo_solicitud = isset($_POST['tipo_solicitud']) ? htmlspecialchars(trim($_POST['tipo_solicitud'])) : '';
    $motivo = isset($_POST['motivo']) ? htmlspecialchars(trim($_POST['motivo'])) : '';
    $datos_paciente_nuevo_json = null;
    $paciente_id_relacionado = null;

    if (empty($tipo_solicitud) || empty($motivo)) {
        throw new Exception('Error: El tipo de solicitud y el motivo son obligatorios.');
    }

    // Lógica para Ingreso y Retiro (igual que antes)
    if ($tipo_solicitud === 'Ingreso') {
        // ... (validaciones y creación de $datos_paciente_nuevo_json) ...
        // Asegúrate que esta parte funcione y genere el JSON correctamente
        // Ejemplo simple para evitar errores aquí:
         $datos_adicionales = [
            'familiar' => ['nombres' => $_POST['familiar_nombre'] ?? 'N/A', /* ... */ 'email' => $_POST['familiar_email'] ?? 'N/A'],
            'paciente' => ['nombres' => $_POST['paciente_nombre'] ?? 'N/A', /* ... */ 'cc' => $_POST['paciente_cc'] ?? 'N/A']
         ];
         $datos_paciente_nuevo_json = json_encode($datos_adicionales, JSON_UNESCAPED_UNICODE);
         if ($datos_paciente_nuevo_json === false) {
             error_log("Error JSON Encode en Ingreso: " . json_last_error_msg());
             throw new Exception('Error interno al procesar datos de ingreso.');
         }

    } elseif ($tipo_solicitud === 'Retiro') {
        // ... (validaciones y asignación de $paciente_id_relacionado) ...
         $paciente_id_a_retirar = isset($_POST['paciente_id_a_retirar']) ? filter_var(trim($_POST['paciente_id_a_retirar']), FILTER_VALIDATE_INT) : null;
         if (empty($paciente_id_a_retirar)) {
             throw new Exception('Error: Debe seleccionar un paciente para la solicitud de retiro.');
         }
         $paciente_id_relacionado = $paciente_id_a_retirar;
    }

    // 5. Ejecutar la inserción
     $sql = "INSERT INTO solicitudes (usuario_id, tipo_solicitud, descripcion, datos_paciente_nuevo, paciente_id_relacionado, estado, fecha_creacion)
            VALUES (?, ?, ?, ?, ?, 'Pendiente', NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la inserción: " . $conn->error);
    }

    // Log ANTES de bind_param
    error_log("[enviar_solicitud] Intentando insertar: UsuarioID={$usuario_id}, Tipo={$tipo_solicitud}, Desc={$motivo}, PacRelID={$paciente_id_relacionado}, JSON={$datos_paciente_nuevo_json}");

    $stmt->bind_param("isssi", $usuario_id, $tipo_solicitud, $motivo, $datos_paciente_nuevo_json, $paciente_id_relacionado);

    if ($stmt->execute()) {
        $new_solicitud_id = $conn->insert_id; // Obtener ID de la solicitud insertada
        $response['success'] = true;
        $response['message'] = 'Solicitud enviada correctamente.';
        error_log("[enviar_solicitud] Éxito al insertar solicitud ID: {$new_solicitud_id} para Usuario ID: {$usuario_id}"); // Log de éxito
    } else {
         error_log("[enviar_solicitud] FALLO al ejecutar insert para Usuario ID: {$usuario_id} - Error: " . $stmt->error); // Log de fallo
        throw new Exception("Error al guardar la solicitud: (" . $stmt->errno . ") " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Captura cualquier excepción
    $response['message'] = $e->getMessage();
    // Registrar el error real para depuración interna
    // Asegúrate que $usuario_id esté definido o pon un valor por defecto
    $log_usuario_id = isset($usuario_id) ? $usuario_id : 'DESCONOCIDO';
    error_log("[enviar_solicitud] Excepción para Usuario ID: {$log_usuario_id} - Error: " . $e->getMessage());
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn && $conn->ping()) $conn->close();
}

// Siempre imprimir la respuesta JSON al final
echo json_encode($response);
exit();
?>