<?php
// Iniciar la sesión para poder acceder a ella y destruirla
session_start(); 

// Eliminar todas las variables de sesión
session_unset(); 

// Destruir la sesión actual
session_destroy();

// Redirigir al usuario a la página de inicio de sesión.
header("Location: ../../views/index-login/htmls/index.php"); 

// El script se detiene después de la redirección
exit();
?>
