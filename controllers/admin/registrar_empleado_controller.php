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
    
    // Generar contraseña temporal aleatoria
    $clave_temporal = bin2hex(random_bytes(5));

    // Hashear esa contraseña temporal
    $contraseña_hashed = password_hash($clave_temporal, PASSWORD_DEFAULT);

    // Recoger los datos del formulario
    $datos = [
        'tipo_documento'           => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'direccion'                => $_POST['direccion'],
        'correo_electronico'       => $_POST['correo_electronico'],
        'contraseña'               => $contraseña_hashed,
        'numero_telefono'          => $_POST['numero_telefono'],
        'fecha_contratacion'       => $_POST['fecha_contratacion'],
        'tipo_contrato'            => $_POST['tipo_contrato'],
        'contacto_emergencia'      => $_POST['contacto_emergencia'],
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'],
        'parentesco'               => null,
        'nombre_rol'               => $_POST['nombre_rol'],
    ];

    try {
        $usuario = new administrador();
        // Llama al método del modelo
        $usuario->registrarEmpleado($datos); 

        // Muestra la contraseña temporal al administrador
        $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado. Contraseña temporal: " . $clave_temporal;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error al registrar: " . $e->getMessage();
    }

    // Redirigir siempre al panel de administración
    header("Location: ../../views/admin/html_admin/admin_pacientes.php");
    exit;
}
?>
