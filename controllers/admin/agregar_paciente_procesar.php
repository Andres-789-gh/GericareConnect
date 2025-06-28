<?php
// --- Manejador de Errores Avanzado para Depuración ---
// Este bloque es crucial para atrapar errores fatales (ej. archivo no encontrado) y devolverlos como JSON.
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo json_encode([
            'success' => false,
            'message' => "Error fatal en el script del servidor.",
            'debug_error' => [
                'type'    => $error['type'],
                'message' => $error['message'],
                'file'    => $error['file'],
                'line'    => $error['line']
            ]
        ]);
    }
});
// --- Fin del Manejador de Errores ---

session_start();
require_once __DIR__ . '/../../models/clases/pacientes.php';

$response = ['success' => false, 'message' => 'Error: Solicitud no válida.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos_paciente = [
        'documento_identificacion' => filter_input(INPUT_POST, 'documento_identificacion', FILTER_SANITIZE_NUMBER_INT),
        'nombre'                   => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
        'apellido'                 => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING),
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'] ?? null,
        'genero'                   => $_POST['genero'] ?? null,
        'contacto_emergencia'      => filter_input(INPUT_POST, 'contacto_emergencia', FILTER_SANITIZE_STRING),
        'estado_civil'             => $_POST['estado_civil'] ?? null,
        'tipo_sangre'              => $_POST['tipo_sangre'] ?? null,
        'seguro_medico'            => filter_input(INPUT_POST, 'seguro_medico', FILTER_SANITIZE_STRING),
        'numero_seguro'            => filter_input(INPUT_POST, 'numero_seguro', FILTER_SANITIZE_STRING),
        'id_usuario_familiar'      => filter_input(INPUT_POST, 'familiar_solicitante_id', FILTER_VALIDATE_INT) ?: null
    ];
    
    $errores = [];
    if (empty($datos_paciente['nombre'])) $errores[] = "El nombre es requerido.";
    if (empty($datos_paciente['apellido'])) $errores[] = "El apellido es requerido.";
    if (empty($datos_paciente['documento_identificacion'])) $errores[] = "El número de documento es requerido.";
    
    if (empty($errores)) {
        try {
            $paciente = new Paciente();
            $resultado = $paciente->registrar($datos_paciente);

            if ($resultado && isset($resultado['id_paciente_creado'])) {
                $response['success'] = true;
                $response['message'] = "¡Paciente '{$datos_paciente['nombre']}' agregado con éxito!";
            } else {
                $response['message'] = "El registro fue procesado pero no se recibió confirmación de la base de datos.";
            }

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            $response['debug_error'] = [ 'file' => $e->getFile(), 'line' => $e->getLine() ];
        }
    } else {
        $response['message'] = implode("<br>", $errores);
    }
}

echo json_encode($response);
?>
