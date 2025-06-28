<?php

// La conexión a 'conexion.php' ahora se hace desde la misma carpeta 'models/clases/'
require_once "conexion.php";

class ModeloEnfermedades {

    /*=============================================
    Método para Crear una Enfermedad
    =============================================*/
    static public function mdlCrearEnfermedad($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre_enfermedad, descripcion_enfermedad, estado) VALUES (:nombre_enfermedad, :descripcion_enfermedad, :estado)");

        $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    Método para Mostrar Enfermedades
    =============================================*/
    static public function mdlMostrarEnfermedades($tabla, $item, $valor) {
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
    Método para Editar una Enfermedad
    =============================================*/
    static public function mdlEditarEnfermedad($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre_enfermedad = :nombre_enfermedad, descripcion_enfermedad = :descripcion_enfermedad WHERE id_enfermedad = :id_enfermedad");

        $stmt->bindParam(":nombre_enfermedad", $datos["nombre_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_enfermedad", $datos["descripcion_enfermedad"], PDO::PARAM_STR);
        $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    Método para Actualizar el Estado de una Enfermedad
    =============================================*/
    static public function mdlActualizarEstadoEnfermedad($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id_enfermedad = :id_enfermedad");

        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":id_enfermedad", $datos["id_enfermedad"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}
?>