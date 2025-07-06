<?php
session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

// Verificar que el usuario sea administrador
verificarAcceso(['Administrador']);

// Definir las rutas de redirección
$vista_principal = '/GericareConnect/views/admin/html_admin/historia_clinica.php';
$vista_formulario = '/GericareConnect/views/admin/html_admin/form_historia_clinica.php';

// Comprobar que la solicitud sea por método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $vista_principal");
    exit();
}

try {
    // Recoger los datos del formulario
    $datos = [
        'id_historia_clinica'       => $_POST['id_historia_clinica'] ?? null,
        'id_paciente'               => $_POST['id_paciente'],
        'id_usuario_administrador'  => $_SESSION['id_usuario'], // El admin que está logueado
        'estado_salud'              => $_POST['estado_salud'],
        'condiciones'               => $_POST['condiciones'],
        'antecedentes_medicos'      => $_POST['antecedentes_medicos'],
        'alergias'                  => $_POST['alergias'],
        'dietas_especiales'         => $_POST['dietas_especiales'],
        'fecha_ultima_consulta'     => $_POST['fecha_ultima_consulta'],
        'observaciones'             => $_POST['observaciones']
    ];

    $accion = $_POST['accion'];
    $modelo = new HistoriaClinica();

    if ($accion === 'registrar') {
        $modelo->registrarHistoria($datos);
        $_SESSION['mensaje'] = "Historia clínica registrada con éxito.";
    } elseif ($accion === 'actualizar') {
        if (empty($datos['id_historia_clinica'])) {
            throw new Exception("No se proporcionó el ID de la historia clínica para actualizar.");
        }
        $modelo->actualizarHistoria($datos);
        $_SESSION['mensaje'] = "Historia clínica actualizada correctamente.";
    } else {
        throw new Exception("Acción no reconocida.");
    }

    // Redirigir a la vista principal si todo fue bien
    header("Location: $vista_principal");
    exit();

} catch (Exception $e) {
    // Manejo de errores
    $_SESSION['error'] = "Error al procesar la historia clínica: " . $e->getMessage();
    // Si hay un ID, es una edición, así que volvemos al formulario con ese ID
    $id_param = isset($datos['id_historia_clinica']) ? '?id=' . $datos['id_historia_clinica'] : '';
    header("Location: " . $vista_formulario . $id_param);
    exit();
}
?>