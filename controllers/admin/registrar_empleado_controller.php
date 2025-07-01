<?php
session_start();
require_once (__DIR__ . '/../../models/clases/usuario.php');

// Verificar que solo un administrador pueda ejecutar este script
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validación de Contraseña
    if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
        $_SESSION['error_registro'] = "Las contraseñas no coinciden.";
        header("Location: ../../views/admin/html_admin/registrar_empleado.php");
        exit();
    }
    if (strlen($_POST['contrasena']) < 6) {
        $_SESSION['error_registro'] = "La contraseña debe tener al menos 6 caracteres.";
        header("Location: ../../views/admin/html_admin/registrar_empleado.php");
        exit();
    }

    // Hashear la contraseña
    $contrasena_hashed = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Recoger los datos del formulario
    $datos = [
        'tipo_documento'           => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'direccion'                => $_POST['direccion'],
        'correo_electronico'       => $_POST['correo_electronico'],
        'contrasena'               => $contrasena_hashed, // Usar la contraseña hasheada
        'numero_telefono'          => $_POST['numero_telefono'],
        'fecha_contratacion'       => $_POST['fecha_contratacion'],
        'tipo_contrato'            => $_POST['tipo_contrato'],
        'contacto_emergencia'      => $_POST['contacto_emergencia'],
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'],
        'parentesco'               => null, // Es un empleado asi que no tiene parentesco
        'nombre_rol'               => $_POST['nombre_rol'],
    ];

    try {
        $usuario = new administrador();
        $usuario->registrarEmpleado($datos);
        
        $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado correctamente.";

    } catch (Exception $e) {
        // Capturar el mensaje de error de la base de datos
        $_SESSION['error'] = "Error al registrar: " . $e->getMessage();
    }

    // Redirigir siempre al panel de administración
    header("Location: ../../views/admin/html_admin/admin_pacientes.php");
    exit;
}
?>
