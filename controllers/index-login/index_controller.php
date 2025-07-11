<?php
// Iniciar o reanudar sesión. Es lo primero que se hace porque se va a guardar
// información del usuario (como su ID y rol) si el login es exitoso.
session_start();

// "require_once" es como un include, pero se asegura de que el archivo solo se cargue una vez.
// Aquí estamos "trayendo" la clase usuario para poder usar sus métodos, (el de Login).
require_once __DIR__ . '/../../models/clases/usuario.php';

// Se define una variable para no tener que escribir la ruta completa de la página de login cada vez. Para mantener el código limpio.
$login_location = '/GericareConnect/views/index-login/htmls/index.php';

// Se crea un nuevo objeto de la clase 'usuario'.
// Ahora, la variable '$acceso_usuario' tiene acceso a todos los métodos de la clase (Login, Actualizar, etc.).
$acceso_usuario = new usuario();

// Se llama al método 'Login' del objeto '$acceso_usuario'.
// Se le pasa el tipo de documento y el número de documento que el usuario escribió en el formulario.
// No se envía la contraseña aquí. Primero se busca al usuario por su documento y si existe, luego se verifica la contraseña. Es más seguro.
$respuesta = $acceso_usuario->Login($_POST["tipo_documento"], $_POST["documento"]);

// Si el método 'Login' lanzó una excepción (un error, como que la base de datos no responde),
// se redirige al usuario a una página de error 500.
if($respuesta instanceof Exception){
    header("location:../../views/index-login/htmls/error500.html");
    exit(); // 'exit()' detiene la ejecución del script para que no siga corriendo.
}

// Se verifica si la variable '$respuesta' NO está vacía.
// Si no está vacía significa que la base de datos encontró un usuario con ese documento.
if(!empty($respuesta)) {
    
    // Se comprueba si la respuesta de la base de datos contiene la columna "nombre_rol".
    // Es una doble verificación para asegurarse de que estan los datos que se necesitan.
    if(isset($respuesta[0]["nombre_rol"])) {

        //  Paso importate para seguridad
        // 'password_verify()' es la función de PHP para comparar una contraseña en texto plano
        // (lo que el usuario escribió) con una contraseña "hasheada" (la que está en la base de datos).
        if (password_verify($_POST["password"], $respuesta[0]["contraseña"])) {

            // Si la contraseña es correcta, se guardan los datos importantes del usuario en la sesión.
            // Con eso, en cualquier otra página, se puede saber quién es el usuario y qué rol tiene.
            $_SESSION['id_usuario'] = $respuesta[0]["id_usuario"];
            $_SESSION['nombre_rol'] = $respuesta[0]["nombre_rol"];
            
            // Se usa un 'switch' para redirigir al usuario al panel que le corresponde según su rol.
            switch($respuesta[0]["nombre_rol"]) {
                case "Administrador":
                    header("location:../../views/admin/html_admin/admin_pacientes.php");
                    break;
                case "Cuidador":
                    header("location:../../views/cuidador/html_cuidador/cuidadores_panel_principal.php");
                    break;
                case "Familiar":
                    header("location:../../views/familiar/html_familiar/familiares.php");
                    break;
                default:
                    // Si el rol no es ninguno de los conocidos, se manda un error.
                    $_SESSION['error_login'] = "Rol de usuario no reconocido.";
                    header("Location: $login_location");
                    exit();
            }
            exit(); // Se detiene el script después de redirigir.

        } else {
            // Si 'password_verify' falla, significa que la contraseña es incorrecta.
            // Se guarda un mensaje de error genérico en la sesión por seguridad.
            // No le decimos al usuario si fue el documento o la contraseña lo que falló.
            $_SESSION['error_login'] = "El tipo de documento, número de documento o la contraseña son incorrectos.";
            header("Location: $login_location");
            exit();
        }

    } else {
        // Este error ocurriría si la consulta a la base de datos no devolvió el rol.
        $_SESSION['error_login'] = "No se pudo determinar el rol del usuario.";
        header("Location: $login_location");
        exit();
    }

} else {
    // Si '$respuesta' está vacía, significa que no se encontró ningún usuario con ese documento.
    $_SESSION['error_login'] = "Datos incorrectos.";
    header("Location: $login_location");
    exit();
}
?>