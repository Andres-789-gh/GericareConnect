<?php
require_once (__DIR__ . '/../../models/clases/usuario.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Generar contraseña aleatoria (10 caracteres hexadecimales)
    $clave_temporal = bin2hex(random_bytes(5));

    // Obtener y sanitizar datos
    $datos = [
        'tipo_documento'           => $_POST['tipo_documento'] ?? '',
        'documento_identificacion' => $_POST['documento_identificacion'] ?? '',
        'nombre'                   => $_POST['nombre'] ?? '',
        'apellido'                 => $_POST['apellido'] ?? '',
        'direccion'                => $_POST['direccion'] ?? '',
        'correo_electronico'       => $_POST['correo_electronico'] ?? '',
        'contraseña'               => password_hash($clave_temporal, PASSWORD_DEFAULT),
        'numero_telefono'          => $_POST['numero_telefono'] ?? '',
        'parentesco'               => $_POST['parentesco'] ?? '',
        'rol'                      => 'Familiar' // Se define estáticamente
    ];

    session_start();

    // Validación de campo parentesco obligatorio
    if (empty($datos['parentesco'])) {
        $_SESSION['error'] = 'Debes completar el campo "Parentesco con el paciente".';
        header('Location: ../../views/familiar/html_familiar/registro_familiar_view.php');
        exit();
    }

    try {
        $usuario = new familiar(); // Se usa la clase específica

        // Eliminar campo 'rol' antes de pasarlo si no se usa en el SP
        unset($datos['rol']); 

        $usuario->registrar($datos);

        $_SESSION['mensaje'] = 'Familiar registrado correctamente. Contraseña temporal: ' . $clave_temporal;

    } catch (PDOException $e) {
        $mensaje = $e->errorInfo[2] ?? '';

        if (str_contains($mensaje, 'Ya existe un usuario')) {
            $_SESSION['error'] = 'Ya existe un usuario con ese número de documento.';
        } else if ($e->getCode() == 23000) {
            $_SESSION['error'] = 'Ya existe un usuario con este documento o correo.';
        } else {
            $_SESSION['error'] = 'Ha ocurrido un error. Intenta nuevamente.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error inesperado: ' . $e->getMessage();
    }

    header('Location: ../../views/familiar/html_familiar/registro_familiar_view.php');
    exit();

} else {
    session_start();
    $_SESSION['error'] = 'Acceso no permitido.';
    header('Location: ../../views/familiar/html_familiar/registro_familiar_view.php');
    exit();
}
