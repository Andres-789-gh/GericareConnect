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
        // Aquí se puede incluir la vista pasando $datosUsuario
        include_once (__DIR__ . '/../../views/index-login/htmls/actualizar_usuario.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../views/admin/html_admin/admin_pacientes.php");
        exit;
    }
}

// Aquí se guardan los datos nuevos que el usuario actualizó en el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos = [
        'id_usuario' => $_POST['id_usuario'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'fecha_nacimiento' => $_POST['fecha_nacimiento'],
        'direccion' => $_POST['direccion'],
        'correo_electronico' => $_POST['correo_electronico'],
        'numero_telefono' => $_POST['numero_telefono'] ?? null,
        'fecha_contratacion' => $_POST['fecha_contratacion'] ?? null,
        'tipo_contrato' => $_POST['tipo_contrato'] ?? null,
        'contacto_emergencia' => $_POST['contacto_emergencia'] ?? null,
        'parentesco' => $_POST['parentesco'] ?? null,
        'estado_usuario' => $_POST['estado_usuario'],
        'roles' => implode(',', $_POST['roles'] ?? []),
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
