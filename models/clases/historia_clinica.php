<?php
require_once __DIR__ . '/../data_base/database.php';

class HistoriaClinica {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // ... (MÉTODOS ANTERIORES SE MANTIENEN IGUAL) ...
    public function consultarHistorias($busqueda = null) {
        try {
            // Este CALL ahora devuelve los contadores med_count y enf_count
            $stmt = $this->conn->prepare("CALL consultar_historia_clinica(NULL, ?)");
            $stmt->execute([$busqueda]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en consultarHistorias: " . $e->getMessage());
            return [];
        }
    }
    public function obtenerHistoriaPorId($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("CALL consultar_historia_clinica(?, NULL)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerHistoriaPorId: " . $e->getMessage());
            return null;
        }
    }
    public function registrarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("CALL registrar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id_paciente'], $datos['id_usuario_administrador'], $datos['estado_salud'], $datos['condiciones'], $datos['antecedentes_medicos'], $datos['alergias'], $datos['dietas_especiales'], $datos['fecha_ultima_consulta'], $datos['observaciones']]);
            return true;
        } catch (Exception $e) { throw $e; }
    }
    public function actualizarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("CALL actualizar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id_historia_clinica'], $datos['id_usuario_administrador'], $datos['estado_salud'], $datos['condiciones'], $datos['antecedentes_medicos'], $datos['alergias'], $datos['dietas_especiales'], $datos['fecha_ultima_consulta'], $datos['observaciones']]);
            return true;
        } catch (Exception $e) { throw $e; }
    }
    public function desactivarHistoria($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("CALL eliminar_historia_clinica(?)");
            $stmt->execute([$id_historia_clinica]);
            return true;
        } catch (Exception $e) { throw $e; }
    }
    public function consultarEnfermedadesAsignadas($id_historia_clinica) {
        $stmt = $this->conn->prepare("CALL consultar_enfermedades_hc(?)");
        $stmt->execute([$id_historia_clinica]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function asignarEnfermedad($id_hc, $id_enfermedad) {
        $stmt = $this->conn->prepare("CALL asignar_enfermedad_hc(?, ?)");
        $stmt->execute([$id_hc, $id_enfermedad]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function eliminarEnfermedadAsignada($id_hc_enfermedad) {
        $stmt = $this->conn->prepare("CALL eliminar_enfermedad_hc(?)");
        return $stmt->execute([$id_hc_enfermedad]);
    }
    public function consultarMedicamentosAsignados($id_historia_clinica) {
        $stmt = $this->conn->prepare("CALL consultar_medicamentos_hc(?)");
        $stmt->execute([$id_historia_clinica]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function asignarMedicamento($datos) {
        $stmt = $this->conn->prepare("CALL asignar_medicamento_hc(?, ?, ?, ?, ?)");
        $stmt->execute([$datos['id_historia_clinica'], $datos['id_medicamento'], $datos['dosis'], $datos['frecuencia'], $datos['instrucciones']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function actualizarMedicamentoAsignado($datos) {
        $stmt = $this->conn->prepare("CALL actualizar_medicamento_hc(?, ?, ?, ?)");
        return $stmt->execute([$datos['id_hc_medicamento'], $datos['dosis'], $datos['frecuencia'], $datos['instrucciones']]);
    }
    public function eliminarMedicamentoAsignado($id_hc_medicamento) {
        $stmt = $this->conn->prepare("CALL eliminar_medicamento_hc(?)");
        return $stmt->execute([$id_hc_medicamento]);
    }

    // --- NUEVO MÉTODO PARA EL REPORTE ---
    public function obtenerReporteCompleto($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("CALL consultar_reporte_completo_hc(?)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReporteCompleto: " . $e->getMessage());
            return null;
        }
    }
}
?>