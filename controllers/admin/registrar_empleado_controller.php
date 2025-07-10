<?php
session_start();
require_once (__DIR__ . '/../../models/clases/usuario.php');

$form_location = '../../views/admin/html_admin/registrar_empleado.php';
$exito_location = '../../views/admin/html_admin/admin_pacientes.php';

// Verificar que solo un administrador pueda ejecutar este script
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: /GericareConnect/views/index-login/htmls/index.php");
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

        $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado correctamente. Se ha enviado la contraseña al correo electrónico del usuario.";
        // $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado. Contraseña temporal: " . $clave_temporal;

        // logica    para envira el correo con la contraseña
        $correo_destinatario = $datos['correo_electronico'];
        $nombre_destinatario = $datos['nombre'];
        $asunto = "Bienvenido a GeriCare Connect - Su Contraseña Temporal";

        // Cuerpo del correo en formato HTML
        $cuerpo_correo = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <h2>¡Hola, " . htmlspecialchars($nombre_destinatario) . "!</h2>
            <p>Te damos la bienvenida a <strong>GeriCare Connect</strong>. Tu cuenta de empleado ha sido creada exitosamente.</p>
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

        // Si todo es exitoso se redirige al panel del admin
        header("Location: $exito_location");
        exit;

    } catch (Exception $e) {
        // manejo de errores
        $errorMessage = $e->getMessage();
        if (str_contains($errorMessage, 'documento')) {
            $_SESSION['error_registro'] = 'El documento de identificación ingresado ya pertenece a otro usuario.';
        } elseif (str_contains($errorMessage, 'correo')) {
            $_SESSION['error_registro'] = 'El correo electrónico ingresado ya pertenece a otro usuario.';
        } else {
            $_SESSION['error_registro'] = 'Ocurrió un error en la base de datos. Por favor, intente de nuevo.';
        }
    } catch (Exception $e) {
        $_SESSION['error_registro'] = "Se produjo un error inesperado. Por favor, intente de nuevo.";
    }

    // Si hubo un error redirige de vuelta al form
    header("Location: $form_location");
    exit;
}
?>
