<?php
require_once (__DIR__ . '/../../models/clases/usuario.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Generar contraseña aleatoria (10 caracteres hexadecimales)
    $clave_temporal = bin2hex(random_bytes(5));

    // Obtener y sanitizar datos
    $datos = [
        'tipo_documento'            => $_POST['tipo_documento'] ?? '',
        'documento_identificacion'  => $_POST['documento_identificacion'] ?? '',
        'nombre'                    => $_POST['nombre'] ?? '',
        'apellido'                  => $_POST['apellido'] ?? '',
        'fecha_nacimiento'          => $_POST['fecha_nacimiento'] ?? '',
        'direccion'                 => $_POST['direccion'] ?? '',
        'correo_electronico'        => $_POST['correo_electronico'] ?? '',
        'contraseña'                => password_hash($clave_temporal, PASSWORD_DEFAULT), // Se hashea la clave generada
        'numero_telefono'           => $_POST['numero_telefono'] ?? '',
        'fecha_contratacion'        => $_POST['fecha_contratacion'] ?? '',
        'tipo_contrato'             => $_POST['tipo_contrato'] ?? '',
        'contacto_emergencia'       => $_POST['contacto_emergencia'] ?? '',
        'parentesco'                => $_POST['parentesco'] ?? '',
        'roles'                     => isset($_POST['roles']) ? implode(',', $_POST['roles']) : '',
    ];

    session_start();

    // Validaciones según roles
    $rolesSeleccionados = explode(',', $datos['roles']);

    if (empty($datos['roles'])) {
        $_SESSION['error'] = 'Debes seleccionar al menos un rol.';
        header('Location: ../../views/index-login/htmls/register.php');
        exit();
    }

    if (in_array('Administrador', $rolesSeleccionados) || in_array('Cuidador', $rolesSeleccionados)) {
        if (empty($datos['fecha_contratacion']) || empty($datos['tipo_contrato']) || empty($datos['contacto_emergencia'])) {
            $_SESSION['error'] = 'Debes completar todos los campos laborales si deseas registrarte como administrador o cuidador.';
            header('Location: ../../views/index-login/htmls/register.php');
            exit();
        }
    }

    if (in_array('Familiar', $rolesSeleccionados)) {
        if (empty($datos['parentesco'])) {
            $_SESSION['error'] = 'Debes completar el campo parentesco si deseas registrarte como familiar.';
            header('Location: ../../views/index-login/htmls/register.php');
            exit();
        }
    }

    try {
        $usuario = new usuario();
        $resultado = $usuario->Registrar($datos);

        $_SESSION['mensaje'] = 'Usuario registrado correctamente. Contraseña temporal: ' . $clave_temporal; // se muestra la contraseña esto es temporal

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

    header('Location: ../../views/index-login/htmls/register.php');
    exit();

} else {
    session_start();
    $_SESSION['error'] = 'Acceso no permitido.';
    header('Location: ../../views/index-login/htmls/register.php');
    exit();
}
