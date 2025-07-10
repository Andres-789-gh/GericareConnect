<?php
session_start();
require_once __DIR__ . '/../../models/clases/usuario.php';

/* Definir la ubicación del login para no repetirla */
$login_location = '/GericareConnect/views/index-login/htmls/index.php';

$acceso_usuario = new usuario();
// quitamos la contraseña del llamado al método Login, porque la validaremos manualmente después
$respuesta = $acceso_usuario->Login($_POST["tipo_documento"], $_POST["documento"]);

// Manejar errores de excepción
if($respuesta instanceof Exception){
    header("location:../../views/index-login/htmls/error500.html");
    exit();
}

// Verificar si hay resultados
if(!empty($respuesta)) {
    // Verificar que el índice nombre_rol existe
    if(isset($respuesta[0]["nombre_rol"])) {

        // Verificar contraseña con password_verify
        if (password_verify($_POST["password"], $respuesta[0]["contraseña"])) {

            $_SESSION['id_usuario'] = $respuesta[0]["id_usuario"];
            $_SESSION['nombre_rol'] = $respuesta[0]["nombre_rol"];
            
            // Redirigir según el rol
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
                    $_SESSION['error_login'] = "Rol de usuario no reconocido.";
                    header("Location: $login_location");
                    exit();
            }
            exit();

        } else {
            $_SESSION['error_login'] = "El tipo de documento, número de documento o la contraseña son incorrectos.";
            header("Location: $login_location");
            exit();
        }

    } else {
        $_SESSION['error_login'] = "No se pudo determinar el rol del usuario.";
        header("Location: $login_location");
        exit();
    }

} else {
    $_SESSION['error_login'] = "Datos incorrectos";
    header("Location: $login_location");
    exit();
}
?>