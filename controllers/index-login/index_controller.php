<?php
session_start();
include "../../models/clases/usuario.php";

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
                    echo "<script>
                        alert('Rol no reconocido: " . $respuesta[0]["nombre_rol"] . "');
                        window.history.back();
                    </script>";
            }

        } else {
            echo "<script>
                alert('Contraseña incorrecta');
                window.history.back();
            </script>";
        }

    } else {
        echo "<script>
            alert('Error: No se pudo determinar el rol del usuario');
            window.history.back();
        </script>";
    }

} else {
    echo "<script>
        alert('Datos incorrectos');
        window.history.back();
    </script>";
}
?>