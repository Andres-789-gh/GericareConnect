<?php
require_once __DIR__ . '/../auth/verificar_sesion.php';
verificarAcceso();
require_once (__DIR__ . '/../../models/clases/usuario.php');

/* Definir la URL de retorno dependiendo del rol */
$url_retorno = '../../views/index-login/htmls/index.html'; // URL por defecto
if (isset($_SESSION['nombre_rol'])) {
    switch ($_SESSION['nombre_rol']) {
        case 'Administrador':
            $url_retorno = '../../views/admin/html_admin/admin_pacientes.php';
            break;
        case 'Cuidador':
            $url_retorno = '../../views/cuidador/html_cuidador/cuidadores_panel_principal.php';
            break;
        case 'Familiar':
            $url_retorno = '../../views/familiar/html_familiar/familiares.php';
            break;
    }
}

// Instancia de la clase de usuario
$usuario = new usuario();

// Si se recibe una solicitud GET con un ID, se obtienen los datos del usuario para mostrarlos en el formulario de actualización.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        // Obtiene los datos del usuario por su ID
        $datosUsuario = $usuario->obtenerPorId($_GET['id']);
        if (!$datosUsuario) {
            // Si no se encuentra el usuario, se establece un mensaje de error y se redirige.
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location:" . $url_retorno);
            exit;
        }

        // Incluye la vista del formulario de actualización
        include_once (__DIR__ . '/../../views/index-login/htmls/actualizar_usuario.php');
        exit;

    } catch (Exception $e) {
        // Si ocurre un error, se establece un mensaje de error y se redirige.
        $_SESSION['error'] = $e->getMessage();
        header("Location:" . $url_retorno);
        exit;
    }
}

// Si se recibe una solicitud POST se procesa la actualización de los datos del usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validación del Telefono y que solo sean numeros
    $numero_telefono = trim($_POST['numero_telefono'] ?? '');
    if (empty($numero_telefono) || !ctype_digit($numero_telefono)) {
        $_SESSION['error'] = 'El número de teléfono es obligatorio y solo debe contener números.';
        header("Location: /GericareConnect/controllers/index-login/actualizar_controller.php?id=" . $_POST['id_usuario']);
        exit;
    }
    
    // Limpiar campos dependiendo del rol
    $rol = $_POST['rol'] ?? '';
    $fecha_contratacion = $_POST['fecha_contratacion'] ?? null;
    $tipo_contrato = $_POST['tipo_contrato'] ?? null;
    $contacto_emergencia = $_POST['contacto_emergencia'] ?? null;
    $parentesco = $_POST['parentesco'] ?? null;

    if ($rol === 'Familiar') {
        $fecha_contratacion = null;
        $tipo_contrato = null;
        $contacto_emergencia = null;
    } else {
        $parentesco = null;
    }

    // Construir el array con las variables limpias
    $datos = [
        'id_usuario'               => $_POST['id_usuario'],
        'tipo_documento'           => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'direccion'                => $_POST['direccion'],
        'correo_electronico'       => $_POST['correo_electronico'],
        'numero_telefono'          => $numero_telefono, // Se usan las variables validadas
        'fecha_contratacion'       => $fecha_contratacion,
        'tipo_contrato'            => $tipo_contrato,
        'contacto_emergencia'      => $contacto_emergencia,
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'],
        'parentesco'               => $parentesco,
        'nombre_rol'               => $rol,
    ];

    try {
        $usuario->Actualizar($datos);
        $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Reedirigir dependiendo del rol
    $url_redireccion = '../../views/index-login/htmls/index.html'; // En caso de que algo falle se reedirije al index
    
    if (isset($_SESSION['nombre_rol'])) {
        switch ($_SESSION['nombre_rol']) {
            case 'Administrador':
                $url_redireccion = '../../views/admin/html_admin/admin_pacientes.php';
                break;
            case 'Cuidador':
                $url_redireccion = '../../views/cuidador/html_cuidador/cuidadores_panel_principal.php';
                break;
            case 'Familiar':
                $url_redireccion = '../../views/familiar/html_familiar/familiares.php';
                break;
        }
    }
    
    header("Location: " . $url_redireccion);
    exit;
}
?>