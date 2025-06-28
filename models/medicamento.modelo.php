<?php

require_once "conexion.php"; // Asegúrate de que esta ruta sea correcta

class ModeloMedicamentos {

    /*=============================================
    Método para Crear un Medicamento
    =============================================*/
    static public function mdlCrearMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre_medicamento, descripcion_medicamento, estado) VALUES (:nombre_medicamento, :descripcion_medicamento, :estado)");

        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // <-- Asegúrate de que este bind esté aquí

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    Método para Editar un Medicamento
    =============================================*/
    static public function mdlEditarMedicamento($tabla, $datos) {
        // Asegúrate de que el 'estado' se incluye en la consulta UPDATE
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre_medicamento = :nombre_medicamento, descripcion_medicamento = :descripcion_medicamento, estado = :estado WHERE id_medicamento = :id_medicamento");

        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // <-- Asegúrate de que este bind esté aquí
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);

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
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    Método para Actualizar el Estado del Medicamento (para botones de la tabla)
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

    /*=============================================
    Método para Eliminar un Medicamento
    =============================================*/
    static public function mdlEliminarMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_medicamento = :id_medicamento");
        $stmt->bindParam(":id_medicamento", $datos, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}
?>