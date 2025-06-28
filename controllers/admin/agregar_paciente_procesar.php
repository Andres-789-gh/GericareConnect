<?php
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
            'debug_error' => $error
        ]);
    }
});

session_start();
require_once __DIR__ . '/../../models/clases/pacientes.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = [
        'documento_identificacion' => $_POST['documento_identificacion'] ?? null,
        'nombre' => $_POST['nombre'] ?? null,
        'apellido' => $_POST['apellido'] ?? null,
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'genero' => $_POST['genero'] ?? null,
        'contacto_emergencia' => $_POST['contacto_emergencia'] ?? null,
        'estado_civil' => $_POST['estado_civil'] ?? null,
        'tipo_sangre' => $_POST['tipo_sangre'] ?? null,
        'seguro_medico' => $_POST['seguro_medico'] ?? null,
        'numero_seguro' => $_POST['numero_seguro'] ?? null,
        'id_usuario_familiar' => $_POST['familiar_solicitante_id'] ?? null
    ];

    $errores = [];
    if (empty($datos['nombre'])) $errores[] = "El nombre es requerido.";
    if (empty($datos['apellido'])) $errores[] = "El apellido es requerido.";
    if (empty($datos['documento_identificacion'])) $errores[] = "El número de documento es requerido.";

    if (empty($errores)) {
        try {
            $paciente = new Paciente();
            $resultado = $paciente->registrar($datos);

            if ($resultado && isset($resultado['id_paciente_creado'])) {
                $response['success'] = true;
                $response['message'] = "Paciente registrado con ID: " . $resultado['id_paciente_creado'];
            } else {
                $response['message'] = "El procedimiento se ejecutó pero no devolvió un ID válido.";
            }
        } catch (Exception $e) {
            $response['message'] = "Error en el registro: " . $e->getMessage();
        }
    } else {
        $response['message'] = implode("<br>", $errores);
    }
} else {
    $response['message'] = "Método no permitido.";
}

echo json_encode($response);
?>
