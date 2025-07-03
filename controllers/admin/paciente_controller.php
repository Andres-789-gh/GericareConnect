<?php
session_start();
require_once __DIR__ . '/../../models/clases/pacientes.php';

$redirect_location = '../../views/admin/html_admin/admin_pacientes.php';

if (!isset($_POST['accion'])) {
    $_SESSION['error'] = "Acción no especificada.";
    header("Location: $redirect_location");
    exit();
}

try {
    $paciente_model = new Paciente();
    $accion = $_POST['accion'];

    // Preparamos todos los datos del formulario, incluyendo el nuevo campo de alergias
    $datos_formulario = [
        'id_paciente' => $_POST['id_paciente'] ?? null,
        'documento_identificacion' => $_POST['documento_identificacion'] ?? null,
        'nombre' => $_POST['nombre'] ?? null,
        'apellido' => $_POST['apellido'] ?? null,
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'genero' => $_POST['genero'] ?? null,
        'contacto_emergencia' => $_POST['contacto_emergencia'] ?? null,
        'estado_civil' => $_POST['estado_civil'] ?? null,
        'tipo_sangre' => $_POST['tipo_sangre'] ?? null,
        'seguro_medico' => empty($_POST['seguro_medico']) ? null : $_POST['seguro_medico'],
        'numero_seguro' => empty($_POST['numero_seguro']) ? null : $_POST['numero_seguro'],
        'alergias' => empty($_POST['alergias']) ? null : $_POST['alergias'], // Nuevo campo
        'id_usuario_familiar' => empty($_POST['id_usuario_familiar']) ? null : $_POST['id_usuario_familiar'],
    ];

    switch ($accion) {
        case 'registrar':
            $resultado = $paciente_model->registrar($datos_formulario);
            $_SESSION['mensaje'] = "¡Paciente registrado con éxito! ID: " . ($resultado['id_paciente_creado'] ?? 'N/A');
            break;

        case 'actualizar':
            $paciente_model->actualizar($datos_formulario);
            $_SESSION['mensaje'] = "¡Paciente actualizado correctamente!";
            break;

        case 'desactivar':
            $paciente_model->desactivar($_POST['id_paciente'] ?? 0);
            $_SESSION['mensaje'] = "Paciente desactivado.";
            break;

        default:
            $_SESSION['error'] = "Acción no reconocida.";
            break;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Ocurrió un error: " . $e->getMessage();
}

header("Location: $redirect_location");
exit();
?>