<?php
// Inicia una sesión para poder usar variables como $_SESSION['error'], $_SESSION['mensaje'], etc.
session_start();
// Carga el archivo de la clase 'usuario', que contiene la lógica para interactuar con la base de datos.
require_once (__DIR__ . '/../../models/clases/usuario.php');

// Se definen variables para las rutas de redirección.
$form_location = '../../views/admin/html_admin/registrar_empleado.php'; // A dónde volver si hay un error.
$exito_location = '../../views/admin/html_admin/admin_pacientes.php';    // A dónde ir si todo sale bien.

// CAPA DE SEGURIDAD 
// Se verifica que el usuario haya iniciado sesión y que su rol sea 'Administrador'.
// Si no cumple estas condiciones, no puede continuar.
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: /GericareConnect/views/index-login/htmls/index.php"); // Se le envía a la página de login.
    exit(); // Se detiene el script.
}

// Se comprueba que el formulario se haya enviado usando el método POST.
// Esto evita que alguien pueda acceder a este controlador escribiendo la URL en el navegador.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CREACIÓN DE CONTRASEÑA 
    
    // Se genera una contraseña temporal aleatoria.
    // 'random_bytes(5)' crea 5 bytes de datos criptográficamente seguros (que no son legibles, son simbolos extraños tipo: _qC).
    // 'bin2hex()' los convierte a una cadena de texto hexadecimal (10 caracteres) 
    // osea traduce esa cadena de datos binarios ilegibles a su representación en texto hexadecimal.
    $clave_temporal = bin2hex(random_bytes(5));

    // HASH DE CONTRASEÑA
    // 'password_hash()' crea un cifrado seguro que no se puede revertir.
    // PASSWORD_DEFAULT: es una constante de PHP que le dice a la función "password_hash()"
    // que use el algoritmo de cifrado más recomendado que exista actualmente."
    // Y todo eos se guarda en la variable "$contraseña_hashed" para usarla despues.
    $contraseña_hashed = password_hash($clave_temporal, PASSWORD_DEFAULT);

    // Se recogen todos los datos enviados desde el formulario en un array llamado '$datos'.
    // Esto organiza la información para pasarla fácilmente al modelo.
    $datos = [
        'tipo_documento'           => $_POST['tipo_documento'],
        'documento_identificacion' => $_POST['documento_identificacion'],
        'nombre'                   => $_POST['nombre'],
        'apellido'                 => $_POST['apellido'],
        'direccion'                => $_POST['direccion'],
        'correo_electronico'       => $_POST['correo_electronico'],
        'contraseña'               => $contraseña_hashed, // Se guarda la contraseña ya hasheada.
        'numero_telefono'          => $_POST['numero_telefono'],
        'fecha_contratacion'       => $_POST['fecha_contratacion'],
        'tipo_contrato'            => $_POST['tipo_contrato'],
        'contacto_emergencia'      => $_POST['contacto_emergencia'],
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'],
        'parentesco'               => null, // Un empleado no tiene parentesco.
        'nombre_rol'               => $_POST['nombre_rol'],
    ];

    // INTERACCIÓN CON LA BD Y MANEJO DE ERRORES
    try {
        // Se crea un objeto de la clase "administrador" (hereda de usuario).
        $usuario = new administrador();
        // Se llama al método del modelo que se encarga de registrar al empleado en la bd.
        $usuario->registrarEmpleado($datos); 

        // Si el registro es exitoso, se guarda un mensaje de éxito en la sesión.
        $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado correctamente. Se ha enviado la contraseña al correo electrónico del usuario.";
        
        // $_SESSION['mensaje'] = "Empleado '" . htmlspecialchars($datos['nombre']) . "' registrado. Contraseña temporal: " . $clave_temporal;

        // LÓGICA PARA ENVIAR EL CORREO ELECTRÓNICO
        $correo_destinatario = $datos['correo_electronico'];
        $nombre_destinatario = $datos['nombre'];
        $asunto = "Bienvenido a GeriCare Connect - Su Contraseña Temporal";

        // Se construye el cuerpo del correo en formato HTML para que se vea bien.
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

        // Se preparan las cabeceras para indicar que el correo es HTML y tiene caracteres UTF-8.
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // Se define el remitente del correo.
        $headers .= 'From: GeriCare Connect <gericareconnect@gmail.com>' . "\r\n";

        // Se envía el correo. La '@' suprime los errores si el servidor de correo no está bien configurado.
        @mail($correo_destinatario, $asunto, $cuerpo_correo, $headers);

        // Si todo fue exitoso, se redirige al administrador a su panel principal.
        header("Location: $exito_location");
        exit;

    } catch (Exception $e) {
        // MANEJO DE ERRORES AMIGABLE
        // Si ocurre una excepción, el 'try' se detiene y el código salta a el 'catch'.
        $errorMessage = $e->getMessage(); // Se obtiene el mensaje de error técnico de la base de datos.

        // Se revisa el contenido del mensaje técnico para mostrar un error amigable al usuario.
        if (str_contains($errorMessage, 'documento')) {
            $_SESSION['error_registro'] = 'El documento de identificación ingresado ya pertenece a otro usuario.';
        } elseif (str_contains($errorMessage, 'correo')) {
            $_SESSION['error_registro'] = 'El correo electrónico ingresado ya pertenece a otro usuario.';
        } else {
            // Si es otro tipo de error, se muestra un mensaje genérico.
            $_SESSION['error_registro'] = 'Ocurrió un error en la base de datos. Por favor, intente de nuevo.';
        }
    }

    // Si el código llega hasta aquí, es porque ocurrió un error en el 'try'.
    // Y se redirige al usuario de vuelta al formulario de registro para que pueda corregir los datos.
    header("Location: $form_location");
    exit;
}
?>