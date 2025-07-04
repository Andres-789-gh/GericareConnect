<?php
class entradaSalida {
    protected $conn;

    public function __construct() {
        // Incluye conexión BD
        include_once(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_entrada_salida(?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['id_usuario_cuidador'],
                $datos['id_paciente'],
                $datos['tipo_movimiento'],
                $datos['motivo'],
                $datos['observaciones'],
                $datos['id_usuario_administrador'] // Puede ser null si unj admin no esta involucrado
            ]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function consultar($id_paciente = null) {
        try {
            $query = $this->conn->prepare("CALL consultar_historial_paciente(?)");
            $query->execute([$id_paciente]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actualizarObservaciones($id_registro, $observaciones) {
        try {
            $query = $this->conn->prepare("CALL actualizar_observaciones_registro(?, ?)");
            $query->execute([$id_registro, $observaciones]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>