<?php
session_start();
require_once __DIR__ . '/../../models/clases/actividad.php';

// Definimos las rutas de redirección.
$redirect_location = '../../views/cuidador/html_cuidador/cuidadores_panel_principal.php';
$form_location = '../../views/cuidador/html_cuidador/form_actividad.php';

try {
    // Seguridad: Solo un cuidador logueado puede registrar actividades.
    if (!isset($_SESSION['id_usuario']) || $_SESSION['nombre_rol'] !== 'Cuidador') {
        throw new Exception("Acceso no autorizado.");
    }

    if (empty($_POST['tipo_actividad']) || empty($_POST['id_paciente']) || empty($_POST['fecha_actividad'])) {
        throw new Exception("Los campos Tipo de Actividad, Paciente y Fecha son obligatorios.");
    }

    $actividad_model = new Actividad();
    $accion = $_POST['accion'];

    $datos_formulario = [
        'id_paciente'           => $_POST['id_paciente'],
        'id_usuario_cuidador'   => $_SESSION['id_usuario'], // El ID del cuidador logueado.
        'tipo_actividad'        => $_POST['tipo_actividad'],
        'descripcion_actividad' => $_POST['descripcion_actividad'] ?? '',
        'fecha_actividad'       => $_POST['fecha_actividad'],
        'hora_inicio'           => !empty($_POST['hora_inicio']) ? $_POST['hora_inicio'] : null,
        'hora_fin'              => !empty($_POST['hora_fin']) ? $_POST['hora_fin'] : null
    ];

    if ($accion === 'registrar') {
        $actividad_model->registrar($datos_formulario);
        $_SESSION['mensaje'] = "¡Actividad registrada con éxito!";
    }
    header("Location: $redirect_location");
    exit();

} catch (Exception $e) {
    // Si algo sale mal, volvemos al formulario con un mensaje amigable.
    $_SESSION['error'] = "Error al guardar: " . $e->getMessage();
    header("Location: $form_location");
    exit();
}
?>
