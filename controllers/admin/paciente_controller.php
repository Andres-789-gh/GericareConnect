<?php
session_start();
require_once __DIR__ . '/../../models/clases/pacientes.php';

$redirect_location = '../../views/admin/html_admin/admin_pacientes.php';
$form_location = '../../views/admin/html_admin/agregar_paciente.php';

// Mantener el ID en la URL del formulario si estamos editando
if (isset($_POST['id_paciente'])) {
    $form_location .= '?id=' . $_POST['id_paciente'];
}

try {
    $paciente_model = new Paciente();
    $accion = $_POST['accion'] ?? null;

    $datos_formulario = [];

    if ($accion === 'registrar') {
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['documento_identificacion'])) {
            throw new Exception("Los campos Nombre, Apellido y Documento son obligatorios.");
        }
        if (empty($_POST['id_usuario_cuidador'])) {
            throw new Exception("Es obligatorio asignar un cuidador al paciente.");
        }

        $datos_formulario = [
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
            'id_usuario_familiar'      => !empty($_POST['id_usuario_familiar']) ? $_POST['id_usuario_familiar'] : null,
            'id_usuario_cuidador'      => $_POST['id_usuario_cuidador'],
            'id_usuario_administrador' => $_SESSION['id_usuario'],
            'descripcion_asignacion'   => $_POST['descripcion_asignacion'] ?? null
        ];
        
        $paciente_model->registrar($datos_formulario);
        $_SESSION['mensaje'] = "¡Paciente registrado con éxito!";

    } elseif ($accion === 'actualizar') {
        // Para actualizar, no validamos los campos deshabilitados
        if (empty($_POST['id_usuario_cuidador'])) {
            throw new Exception("Es obligatorio asignar un cuidador al paciente.");
        }

        $datos_formulario = [
            'id_paciente'              => $_POST['id_paciente'],
            'genero'                   => $_POST['genero'] ?? null,
            'contacto_emergencia'      => $_POST['contacto_emergencia'] ?? null,
            'estado_civil'             => $_POST['estado_civil'] ?? null,
            'seguro_medico'            => $_POST['seguro_medico'] ?? null,
            'numero_seguro'            => $_POST['numero_seguro'] ?? null,
            'id_usuario_familiar'      => !empty($_POST['id_usuario_familiar']) ? $_POST['id_usuario_familiar'] : null,
            'id_usuario_cuidador'      => $_POST['id_usuario_cuidador'],
            'id_usuario_administrador' => $_SESSION['id_usuario'],
            'descripcion_asignacion'   => $_POST['descripcion_asignacion'] ?? null
        ];

        $paciente_model->actualizar($datos_formulario);
        $_SESSION['mensaje'] = "¡Paciente actualizado correctamente!";
    
    } else {
        throw new Exception("Acción no válida.");
    }

    header("Location: $redirect_location");
    exit();

} catch (Exception $e) {
    if ($e instanceof PDOException && $e->errorInfo[1] == 1062) {
        $_SESSION['error'] = "El documento de identificación ingresado ya pertenece a otro paciente.";
    } else {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: $form_location");
    exit();
}
?>