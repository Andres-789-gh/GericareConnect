<?php
// models/clases/historia_clinica.modelo.php

require_once "conexion.php";

class ModeloHistoriaClinica
{

    /*=============================================
    MOSTRAR HISTORIAS CLINICAS (CORREGIDO Y UNIFICADO)
    =============================================*/
    static public function mdlMostrarHistoriasClinicas($tabla, $item, $valor)
    {
        // Si se pide un item específico (ej: 'id_historia_clinica' = 5)
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("
                SELECT 
                    hc.*, 
                    CONCAT(p.nombre, ' ', p.apellido) as paciente_nombre_completo 
                FROM $tabla hc
                JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
                WHERE hc.$item = :$item"
            );

            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_INT);
            $stmt->execute();
            // fetch() devuelve una sola fila, perfecto para editar
            return $stmt->fetch(); 
        } else {
            // Si no se pide un item, se ejecuta el procedimiento para traer todo
            $stmt = Conexion::conectar()->prepare("CALL mostrar_historias_clinicas()");
            $stmt->execute();
            // fetchAll() devuelve todas las filas
            return $stmt->fetchAll(); 
        }
        $stmt = null;
    }

    /*=============================================
    CREAR HISTORIA CLINICA
    =============================================*/
    static public function mdlCrearHistoriaClinica($datos)
    {
        $stmt = Conexion::conectar()->prepare("CALL crear_historia_clinica(:id_paciente, :id_usuario_cuidador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :observaciones, :medicamentos_ids, :enfermedades_ids)");

        $stmt->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario_cuidador", $datos["id_usuario_cuidador"], PDO::PARAM_INT);
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":medicamentos_ids", $datos["medicamentos_ids"], PDO::PARAM_STR);
        $stmt->bindParam(":enfermedades_ids", $datos["enfermedades_ids"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }
    
    /*=============================================
    EDITAR HISTORIA CLINICA (COMPLETO)
    =============================================*/
    static public function mdlEditarHistoriaClinica($datos)
    {
        // Llamada al nuevo procedimiento almacenado para la actualización completa
        $stmt = Conexion::conectar()->prepare(
            "CALL actualizar_historia_clinica_completa(:id_historia_clinica, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :observaciones, :medicamentos_ids, :enfermedades_ids)"
        );

        // Enlazamos todos los parámetros necesarios
        $stmt->bindParam(":id_historia_clinica", $datos["id_historia_clinica"], PDO::PARAM_INT);
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":medicamentos_ids", $datos["medicamentos_ids"], PDO::PARAM_STR);
        $stmt->bindParam(":enfermedades_ids", $datos["enfermedades_ids"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            error_log(print_r($stmt->errorInfo(), true));
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
    DESACTIVAR HISTORIA CLINICA (BORRADO LÓGICO)
    =============================================*/
    static public function mdlDesactivarHistoriaClinica($idHistoria)
    {
        $stmt = Conexion::conectar()->prepare("CALL desactivar_historia_clinica(:id_historia_clinica)");
        $stmt->bindParam(":id_historia_clinica", $idHistoria, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
        $stmt = null;
    }
    
    /*=============================================
    Métodos auxiliares para llenar los <select> del formulario
    =============================================*/
    static public function mdlObtenerPacientesActivos()
    {
        $stmt = Conexion::conectar()->prepare("SELECT id_paciente, nombre, apellido FROM tb_paciente WHERE estado = 'Activo' ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // *** NUEVAS FUNCIONES PARA OBTENER DATOS PARA EDICIÓN ***
    static public function mdlMostrarMedicamentosPorHistoria($id_historia_clinica) {
        $stmt = Conexion::conectar()->prepare("
            SELECT m.id_medicamento as id, m.nombre_medicamento as nombre
            FROM tb_historia_clinica_medicamento hcm
            JOIN tb_medicamento m ON hcm.id_medicamento = m.id_medicamento
            WHERE hcm.id_historia_clinica = :id_historia_clinica AND hcm.estado = 'Activo'
        ");
        $stmt->bindParam(":id_historia_clinica", $id_historia_clinica, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function mdlMostrarEnfermedadesPorHistoria($id_historia_clinica) {
        $stmt = Conexion::conectar()->prepare("
            SELECT e.id_enfermedad as id, e.nombre_enfermedad as nombre
            FROM tb_historia_clinica_enfermedad hce
            JOIN tb_enfermedad e ON hce.id_enfermedad = e.id_enfermedad
            WHERE hce.id_historia_clinica = :id_historia_clinica AND hce.estado = 'Activo'
        ");
        $stmt->bindParam(":id_historia_clinica", $id_historia_clinica, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}