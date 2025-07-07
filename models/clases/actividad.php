<?php
require_once __DIR__ . '/../data_base/database.php';

class Actividad {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function registrar($datos) {
        $stmt = $this->conn->prepare("CALL registrar_actividad(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $datos['id_paciente'], $_SESSION['id_usuario'], $datos['tipo_actividad'],
            $datos['descripcion_actividad'], $datos['fecha_actividad'], $datos['hora_inicio'],
            $datos['hora_fin']
        ]);
        return true;
    }

    public function consultar($busqueda = null, $estado_filtro = null) {
        $stmt = $this->conn->prepare("CALL consultar_actividades(?, ?)");
        $stmt->execute([$busqueda, $estado_filtro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id_actividad) {
        // Se usa el mismo SP de consulta, pero se filtra por ID en PHP
        $actividades = $this->consultar(null);
        foreach ($actividades as $actividad) {
            if ($actividad['id_actividad'] == $id_actividad) {
                return $actividad;
            }
        }
        return null;
    }

    public function actualizar($datos) {
        $stmt = $this->conn->prepare("CALL actualizar_actividad(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $datos['id_actividad'], $datos['id_paciente'], $datos['tipo_actividad'],
            $datos['descripcion_actividad'], $datos['fecha_actividad'], $datos['hora_inicio'],
            $datos['hora_fin']
        ]);
        return true;
    }

    public function eliminar($id_actividad) {
        $stmt = $this->conn->prepare("CALL eliminar_actividad(?)");
        $stmt->execute([$id_actividad]);
        return true;
    }

    public function consultarPorCuidador($id_cuidador, $busqueda = null, $estado_filtro = null) {
        $stmt = $this->conn->prepare("CALL consultar_actividades_cuidador(?, ?, ?)");
        $stmt->execute([$id_cuidador, $busqueda, $estado_filtro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoCompletada($id_actividad, $id_cuidador) {
        $stmt = $this->conn->prepare("CALL completar_actividad(?, ?)");
        $stmt->execute([$id_actividad, $id_cuidador]);
        return true;
    }
}
?>