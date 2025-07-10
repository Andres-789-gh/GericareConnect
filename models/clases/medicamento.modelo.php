<?php

class ModeloMedicamentos {
    
    private $conn;

    // Constructor para la conexión
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    // --- Métodos convertidos a no estáticos ---

    public function mdlCrearMedicamento($tabla, $datos) {
        $stmt = $this->conn->prepare("INSERT INTO $tabla(nombre_medicamento, descripcion_medicamento, estado) VALUES (:nombre_medicamento, :descripcion_medicamento, :estado)");
        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    public function mdlMostrarMedicamentos($tabla, $item, $valor) {
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

    public function mdlEditarMedicamento($tabla, $datos) {
        $stmt = $this->conn->prepare("UPDATE $tabla SET nombre_medicamento = :nombre_medicamento, descripcion_medicamento = :descripcion_medicamento WHERE id_medicamento = :id_medicamento");
        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    public function mdlActualizarEstadoMedicamento($tabla, $datos) {
        $stmt = $this->conn->prepare("UPDATE $tabla SET estado = :estado WHERE id_medicamento = :id_medicamento");
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }
}
?>