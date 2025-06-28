<?php

// La conexión a 'conexion.php' ahora se hace desde la misma carpeta 'models/clases/'
require_once "conexion.php";

class ModeloMedicamentos {

    /*=============================================
    Método para Crear un Medicamento
    =============================================*/
    static public function mdlCrearMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre_medicamento, descripcion_medicamento, estado) VALUES (:nombre_medicamento, :descripcion_medicamento, :estado)");

        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    Método para Mostrar Medicamentos
    =============================================*/
    static public function mdlMostrarMedicamentos($tabla, $item, $valor) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND estado = 'Activo'");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = 'Activo'");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    Método para Editar un Medicamento
    =============================================*/
    static public function mdlEditarMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre_medicamento = :nombre_medicamento, descripcion_medicamento = :descripcion_medicamento WHERE id_medicamento = :id_medicamento");

        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    Método para Actualizar el Estado del Medicamento
    =============================================*/
    static public function mdlActualizarEstadoMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id_medicamento = :id_medicamento");

        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}
?>