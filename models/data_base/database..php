<?php
class Database {
    public function conectar() {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=gericare_connect;charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
