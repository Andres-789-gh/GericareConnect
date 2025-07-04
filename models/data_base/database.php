<?php
    // Conexión simple y directa a la base de datos.
    try {
        $conn = new PDO("mysql:host=localhost;dbname=gericare_connect", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e) {
        // Si la conexión falla, el script se detiene y muestra el error.
        die("Error de conexión: " . $e->getMessage());
    }
?>