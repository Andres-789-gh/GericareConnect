<?php
session_start();
header('Content-Type: application/json');

// Asegúrate que la ruta a tu clase es correcta.
require_once __DIR__ . '/../../models/clases/pacientes.php';

$response = ['success' => false, 'message' => 'Error: Solicitud no válida.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Se recolectan los datos usando los 'name' exactos del formulario HTML.
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
        // Este campo es opcional y viene del campo oculto.
        'id_usuario_familiar'      => filter_input(INPUT_POST, 'familiar_solicitante_id', FILTER_VALIDATE_INT) ?: null
    ];
    
    // Validaciones del lado del servidor.
    $errores = [];
    if (empty($datos_paciente['nombre'])) $errores[] = "El nombre es requerido.";
    if (empty($datos_paciente['apellido'])) $errores[] = "El apellido es requerido.";
    if (empty($datos_paciente['documento_identificacion'])) $errores[] = "El número de documento es requerido.";
    if (empty($datos_paciente['fecha_nacimiento'])) $errores[] = "La fecha de nacimiento es requerida.";
    if (empty($datos_paciente['genero'])) $errores[] = "El género es requerido.";
    
    if (empty($errores)) {
        try {
            $paciente = new Paciente();
            $resultado = $paciente->registrar($datos_paciente);

            if ($resultado && isset($resultado['id_paciente_creado'])) {
                $response['success'] = true;
                $response['message'] = "Paciente '{$datos_paciente['nombre']} {$datos_paciente['apellido']}' agregado correctamente.";
                $response['paciente_id'] = $resultado['id_paciente_creado'];
            } else {
                $response['message'] = "Error: No se pudo confirmar el registro en la base de datos.";
            }

        } catch (PDOException $e) {
            $response['message'] = "Error de base de datos: " . $e->getMessage();
            error_log("Error PDO en agregar_paciente_procesar.php: " . $e->getMessage());
        } catch (Exception $e) {
            $response['message'] = "Error inesperado en el servidor: " . $e->getMessage();
            error_log("Error general en agregar_paciente_procesar.php: " . $e->getMessage());
        }
    } else {
        $response['message'] = implode("<br>", $errores);
    }
}

echo json_encode($response);
?>
