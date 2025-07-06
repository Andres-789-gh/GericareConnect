<?php

class ModeloEnfermedades {

    private $conn;

    // Constructor para la conexión
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    // --- Métodos convertidos a no estáticos ---

    public function mdlCrearEnfermedad($tabla, $datos) {
        $stmt = $this->conn->prepare("INSERT INTO $tabla(nombre_enfermedad, descripcion_enfermedad, estado) VALUES (:nombre_enfermedad, :descripcion_enfermedad, :estado)");
        $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public function mdlMostrarEnfermedades($tabla, $item, $valor) {
        if ($item != null) {
            $stmt = $this->conn->prepare("SELECT * FROM $tabla WHERE $item = :$item AND estado = 'Activo'");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM $tabla WHERE estado = 'Activo'");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    public function mdlEditarEnfermedad($tabla, $datos) {
        $stmt = $this->conn->prepare("UPDATE $tabla SET nombre_enfermedad = :nombre_enfermedad, descripcion_enfermedad = :descripcion_enfermedad WHERE id_enfermedad = :id_enfermedad");
        $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public function mdlActualizarEstadoEnfermedad($tabla, $datos) {
        $stmt = $this->conn->prepare("UPDATE $tabla SET estado = :estado WHERE id_enfermedad = :id_enfermedad");
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }
}
?>