<?php
session_start();
require_once __DIR__ . '/../../models/clases/pacientes.php';

$redirect_location = '../../views/admin/html_admin/admin_pacientes.php';
$form_location = '../../views/admin/html_admin/agregar_paciente.php';
if (isset($_POST['id_paciente'])) {
    $form_location .= '?id=' . $_POST['id_paciente'];
}

try {
    if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['documento_identificacion'])) {
        throw new Exception("Los campos Nombre, Apellido y Documento son obligatorios.");
    }

    $paciente_model = new Paciente();
    $accion = $_POST['accion'];
    $id_familiar = !empty($_POST['id_usuario_familiar']) ? $_POST['id_usuario_familiar'] : null;

    $datos_formulario = [
        'id_paciente'              => $_POST['id_paciente'] ?? null,
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'] ?? null,
        'genero'                   => $_POST['genero'] ?? null,
        'contacto_emergencia'      => $_POST['contacto_emergencia'] ?? null,
        'estado_civil'             => $_POST['estado_civil'] ?? null,
        'tipo_sangre'              => $_POST['tipo_sangre'] ?? null,
        'seguro_medico'            => $_POST['seguro_medico'] ?? null,
        'numero_seguro'            => $_POST['numero_seguro'] ?? null,
        'id_usuario_familiar'      => $id_familiar
    ];

    switch ($accion) {
        case 'registrar':
            $paciente_model->registrar($datos_formulario);
            $_SESSION['mensaje'] = "¡Paciente registrado con éxito!";
            break;
        case 'actualizar':
            $paciente_model->actualizar($datos_formulario);
            $_SESSION['mensaje'] = "¡Paciente actualizado correctamente!";
            break;
    }
    header("Location: $redirect_location");
    exit();

} catch (Exception $e) {
    if ($e instanceof PDOException) {
        $_SESSION['error'] = "Error de Base de Datos: " . ($e->errorInfo[1] == 1062 ? "Documento ya existe." : "Verifique los datos.");
    } else {
        $_SESSION['error'] = "Error al guardar: " . $e->getMessage();
    }
    header("Location: $form_location");
    exit();
}
?>
