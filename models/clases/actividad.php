<?php
class Actividad {
    private $conn;

    // Se conecta a la BD usando el método original de tu proyecto.
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    // Llama al SP para registrar una nueva actividad.
    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_actividad(?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['id_paciente'], $datos['id_usuario_cuidador'], $datos['tipo_actividad'],
                $datos['descripcion_actividad'], $datos['fecha_actividad'], $datos['hora_inicio'],
                $datos['hora_fin']
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al registrar actividad: " . $e->getMessage());
        }
    }

    // Llama al SP para consultar todas las actividades de un cuidador específico.
    public function consultarPorCuidador($id_cuidador) {
        try {
            $query = $this->conn->prepare("CALL consultar_actividades_por_cuidador(?)");
            $query->execute([$id_cuidador]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al consultar actividades: " . $e->getMessage());
        }
    }

    // Llama al SP para cancelar una actividad.
    public function desactivar($id_actividad) {
        try {
            $query = $this->conn->prepare("CALL desactivar_actividad(?)");
            $query->execute([$id_actividad]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al desactivar actividad: " . $e->getMessage());
        }
    }
}
?>
