<?php
require_once "conexion.php";

class ModeloHistoriaClinica
{

    /*=============================================
    MOSTRAR HISTORIAS CLINICAS
    =============================================*/
    static public function mdlMostrarHistoriasClinicas($tabla, $item, $valor)
    {
        // Si se pide una historia específica, se usará en el futuro
        if ($item != null) {
            // Lógica para mostrar una sola historia clínica (a implementar si es necesario)
            return null;
        } else {
            $stmt = Conexion::conectar()->prepare("CALL mostrar_historias_clinicas()");
            $stmt->execute();
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
}