<?php
session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';

verificarAcceso(['Cuidador']);

$redirect_location = '/GericareConnect/views/cuidador/html_cuidador/cuidador_actividades.php';

if (!isset($_POST['id_actividad'])) {
    $_SESSION['error'] = "No se proporcionó el ID de la actividad.";
    header("Location: $redirect_location");
    exit();
}

try {
    $id_actividad = $_POST['id_actividad'];
    $id_cuidador = $_SESSION['id_usuario'];

    // Se instancia el modelo de Actividad
    $modelo = new Actividad();
    // Se llama al método  desde el modelo
    $modelo->marcarComoCompletada($id_actividad, $id_cuidador);

    $_SESSION['mensaje'] = "¡Actividad marcada como completada!";

} catch (Exception $e) {
    $_SESSION['error'] = "Error al completar la actividad: " . $e->getMessage();
}

header("Location: $redirect_location");
exit();
?>