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

        // Eliminar campo 'rol' antes de pasarlo
        unset($datos['rol']); 

        $usuario->registrar($datos);

        // $_SESSION['mensaje'] = 'Familiar registrado correctamente. Contraseña temporal: ' . $clave_temporal;
        $_SESSION['mensaje'] = 'Familiar registrado correctamente. Se ha enviado la contraseña al correo electrónico del usuario.';

        // cod para envira el correo con la contraseña
        $correo_destinatario = $datos['correo_electronico'];
        $nombre_destinatario = $datos['nombre'];
        $asunto = "Bienvenido a GeriCare Connect - Su Contraseña";

        // Cuerpo del correo en formato HTML
        $cuerpo_correo = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <h2>¡Hola, " . htmlspecialchars($nombre_destinatario) . "!</h2>
            <p>Te damos la bienvenida a <strong>GeriCare Connect</strong>. Tu cuenta ha sido creada exitosamente.</p>
            <p>Puedes iniciar sesión con tu documento de identificación y la siguiente contraseña:</p>
            <p style='background-color: #f2f2f2; border: 1px solid #ddd; padding: 10px; font-size: 18px; font-weight: bold; text-align: center;'>
                " . htmlspecialchars($clave_temporal) . "
            </p>
            <p>Te recomendamos cambiar tu contraseña después de iniciar sesión por primera vez.</p>
            <p>Gracias,<br>El equipo de GeriCare Connect</p>
        </body>
        </html>
        ";

        // Cabeceras para que el correo se envíe en formato HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // Correo que aparecera como remitente
        $headers .= 'From: GeriCare Connect <gericareconnect@gmail.com>' . "\r\n";

        // Se envía el correo (el @ suprime los errores en pantalla si el servidor no está configurado)
        @mail($correo_destinatario, $asunto, $cuerpo_correo, $headers);

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
