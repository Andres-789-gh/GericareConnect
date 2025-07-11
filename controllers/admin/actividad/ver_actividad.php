<?php

// Se inicia la sesión y se cargan los archivos necesarios.
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/actividad.php';

// 1. Verificar permisos: solo Administradores y Cuidadores pueden ver.
verificarAcceso(['Administrador', 'Cuidador']);

// 2. LÓGICA DE REDIRECCIÓN INTELIGENTE
// Se define a dónde redirigir en caso de éxito o error.
if (isset($_SESSION['nombre_rol']) && $_SESSION['nombre_rol'] === 'Cuidador') {
    $pagina_de_retorno = '/GericareConnect/views/cuidador/html_cuidador/cuidador_actividades.php';
} else {
    $pagina_de_retorno = '/GericareConnect/views/admin/html_admin/admin_actividades.php';
}

// 3. Validar que se reciba un ID por la URL.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "No se ha especificado una actividad para ver.";
    // Se usa la variable para redirigir al lugar correcto.
    header("Location: " . $pagina_de_retorno);
    exit();
}

try {
    // 4. Obtener datos de la actividad.
    $id_actividad = $_GET['id'];
    $modelo_actividad = new Actividad();
    $actividad = $modelo_actividad->obtenerPorId($id_actividad);

    // Si la actividad no existe, se redirige al lugar correcto.
    if (!$actividad) {
        $_SESSION['error'] = "La actividad que intenta ver no existe.";
        header("Location: " . $pagina_de_retorno);
        exit();
    }

    // 5. La URL para el botón "Volver" será la misma que para la redirección.
    $url_volver = $pagina_de_retorno;

    // 6. Cargar la vista (plantilla) para mostrar los datos.
    require_once __DIR__ . '/../../../views/admin/html_admin/ver_actividad.php';
    exit();

} catch (Exception $e) {
    // En caso de cualquier otro error, se redirige al lugar correcto.
    $_SESSION['error'] = "Error al cargar los detalles de la actividad: " . $e->getMessage();
    header("Location: " . $pagina_de_retorno);
    exit();
}
?>