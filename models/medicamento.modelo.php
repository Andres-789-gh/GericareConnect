<?php

require_once "conexion.php";

class ModeloMedicamentos {

    // ... (Mantén mdlCrearMedicamento, mdlMostrarMedicamentos, mdlEditarMedicamento sin cambios en su contenido) ...

    /*=============================================
    Método para Crear un Medicamento (SIN CAMBIOS)
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
    Método para Mostrar Medicamentos (MODIFICADO para filtrar por 'Activo')
    =============================================*/
    static public function mdlMostrarMedicamentos($tabla, $item, $valor) {
        if ($item != null) {
            // Si se busca un item específico, se busca también que esté activo.
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND estado = 'Activo'");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            // Se muestran solo los medicamentos con estado 'Activo'
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = 'Activo'");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    Método para Editar un Medicamento (SIN CAMBIOS en su contenido, pero el controlador no le pasará 'estado' directamente del form)
    =============================================*/
    static public function mdlEditarMedicamento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre_medicamento = :nombre_medicamento, descripcion_medicamento = :descripcion_medicamento WHERE id_medicamento = :id_medicamento"); // Eliminamos 'estado = :estado' de aquí

        $stmt->bindParam(":nombre_medicamento", $datos["nombre_medicamento"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion_medicamento", $datos["descripcion_medicamento"], PDO::PARAM_STR);
        // $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // Este bind ya no es necesario aquí para la edición
        $stmt->bindParam(":id_medicamento", $datos["id_medicamento"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }


    /*=============================================
    Método para Actualizar el Estado del Medicamento (AHORA SERÁ USADO PARA ELIMINAR LÓGICAMENTE)
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
    Método para Eliminar un Medicamento (ELIMINAR ESTE MÉTODO POR COMPLETO O COMENTARLO)
    =============================================*/
    // static public function mdlEliminarMedicamento($tabla, $datos) {
    //     $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_medicamento = :id_medicamento");
    //     $stmt->bindParam(":id_medicamento", $datos, PDO::PARAM_INT);

    //     if ($stmt->execute()) {
    //         return "ok";
    //     } else {
    //         return "error";
    //     }
    //     $stmt = null;
    // }
}
?>