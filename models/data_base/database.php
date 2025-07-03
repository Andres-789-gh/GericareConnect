<?php
class Database {
    public function conectar() {
        try {
            // Conexión usando PDO, que es más seguro.
            $conn = new PDO("mysql:host=localhost;dbname=gericare_connect;charset=utf8", "root", "");
            // Configuración para que PDO nos muestre los errores.
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            // Si la conexión falla, el script se detiene y muestra un error claro.
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>