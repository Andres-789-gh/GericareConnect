<?php
session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';

verificarAcceso(['Administrador']);

$redirect_location = '/GericareConnect/views/admin/html_admin/admin_actividades.php';
$form_location = '/GericareConnect/views/admin/html_admin/form_actividades.php';

if (!isset($_POST['accion'])) {
    header("Location: $redirect_location");
    exit();
}

try {
    $modelo = new Actividad();
    $accion = $_POST['accion'];

    if ($accion === 'eliminar') {
        $modelo->eliminar($_POST['id_actividad']);
        $_SESSION['mensaje'] = "Actividad eliminada correctamente.";
        header("Location: $redirect_location");
        exit();
    }

    // Datos para registrar y actualizar
    $datos = [
        'id_paciente'           => $_POST['id_paciente'],
        'tipo_actividad'        => $_POST['tipo_actividad'],
        'descripcion_actividad' => $_POST['descripcion_actividad'],
        'fecha_actividad'       => $_POST['fecha_actividad'],
        'hora_inicio'           => !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : null,
        'hora_fin'              => !empty($_POST['hora_fin']) ? $_POST['hora_fin'] : null,
    ];

    if ($accion === 'registrar') {
        $modelo->registrar($datos);
        $_SESSION['mensaje'] = "Actividad registrada con éxito.";
    } elseif ($accion === 'actualizar') {
    // Si la acción fue de actualizar regresar al formulario con el ID
        $datos['id_actividad'] = $_POST['id_actividad'];
        $modelo->actualizar($datos);
        $_SESSION['mensaje'] = "Actividad actualizada correctamente.";
    }

    header("Location: $redirect_location");
    exit();

} catch (Exception $e) {
    // Verificar si el error viene de la base de datos
    if ($e instanceof PDOException) {
        // Si es un error de BD
        $_SESSION['error'] = "No se logro guardar la actividad. Verifique que los datos no estén duplicados.";
    } else {
        // Si es otro tipo de error (como una validación), mostrar su mensaje.
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    /* redirección de vuelta al formulario */
    $id_param = ($_POST['accion'] === 'actualizar' && isset($_POST['id_actividad'])) ? '?id=' . $_POST['id_actividad'] : '';
    header("Location: " . $form_location . $id_param);
    exit();
}
?>