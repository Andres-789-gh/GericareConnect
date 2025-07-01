<?php
session_start();
require_once (__DIR__ . '/../../models/clases/usuario.php');

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
            header("Location: ../../views/admin/html_admin/admin_pacientes.php");
            exit;
        }

        // Incluye la vista del formulario de actualización
        include_once (__DIR__ . '/../../views/index-login/htmls/actualizar_usuario.php');
        exit;

    } catch (Exception $e) {
        // Si ocurre un error, se establece un mensaje de error y se redirige.
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../views/admin/html_admin/admin_pacientes.php");
        exit;
    }
}

// Si se recibe una solicitud POST, se procesa la actualización de los datos del usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validacion telefono
    $numero_telefono = trim($_POST['numero_telefono'] ?? '');
    
    // Validar que el teléfono no esté vacío
    if (empty($numero_telefono)) {
        $_SESSION['error'] = 'El número de teléfono es un campo obligatorio.';
        header("Location: /GericareConnect/controllers/index-login/actualizar_controller.php?id=" . $_POST['id_usuario']);
        exit;
    }

    // Validar que el teléfono contenga solo números
    if (!ctype_digit($numero_telefono)) {
        $_SESSION['error'] = 'El número de teléfono solo debe contener números.';
        header("Location: /GericareConnect/controllers/index-login/actualizar_controller.php?id=" . $_POST['id_usuario']);
        exit;
    }

    // Array para almacenar los datos del formulario
    $datos = [
        'id_usuario'            => $_POST['id_usuario'],
        'tipo_documento'        => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                => $_POST['nombre'],
        'apellido'              => $_POST['apellido'],
        'direccion'             => $_POST['direccion'],
        'correo_electronico'    => $_POST['correo_electronico'],
        'numero_telefono'       => $_POST['numero_telefono'] ?? null,
        'fecha_contratacion'    => $_POST['fecha_contratacion'] ?? null,
        'tipo_contrato'         => $_POST['tipo_contrato'] ?? null,
        'contacto_emergencia'   => $_POST['contacto_emergencia'] ?? null,
        'fecha_nacimiento'      => $_POST['fecha_nacimiento'],
        'parentesco'            => $_POST['parentesco'] ?? null,
        'nombre_rol'            => $_POST['rol'] ?? null, // se envía como string
    ];

    try {
        // Llamar al método para actualizar el usuario
        $usuario->Actualizar($datos);
        // Mensaje de éxito en la sesión
        $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    } catch (Exception $e) {
        // Si sale un error, mensaje de error en la sesión
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de administración de pacientes
    header("Location: ../../views/admin/html_admin/admin_pacientes.php");
    exit;
}
?>