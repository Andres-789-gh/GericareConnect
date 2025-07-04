<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarAcceso($rolesPermitidos = null) {
    // El usuario ha iniciado sesión?
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: /gericareconnect/views/index-login/htmls/index.html");
        exit();
    }

    // Se requiere un rol específico?
    if ($rolesPermitidos !== null) {
        // Si el rol del usuario no está en la lista de roles permitidos
        if (!in_array($_SESSION['nombre_rol'], $rolesPermitidos)) {
            http_response_code(403); // Acceso Prohibido
            echo "<h1>Acceso Denegado</h1>";
            echo "<p>No tienes los permisos necesarios para acceder a esta página.</p>";
            exit();
        }
    }
}
?>