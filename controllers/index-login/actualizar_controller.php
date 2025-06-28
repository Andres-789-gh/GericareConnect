<?php
session_start();
require_once (__DIR__ . '/../../models/clases/usuario.php');

$usuario = new usuario();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $datosUsuario = $usuario->obtenerPorId($_GET['id']);
        if (!$datosUsuario) {
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location: ../../views/admin/html_admin/admin_pacientes.php");
            exit;
        }

        include_once (__DIR__ . '/../../views/index-login/htmls/actualizar_usuario.php');
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../views/admin/html_admin/admin_pacientes.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        'roles'                 => $_POST['rol'] // se envía como string, no array
    ];

    try {
        $usuario->Actualizar($datos);
        $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: ../../views/admin/html_admin/admin_pacientes.php");
    exit;
}
?>
