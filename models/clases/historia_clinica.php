<?php

class HistoriaClinica {
    private $conn;

    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    /* Consulta una lista de historias clínicas, opcionalmente con un término de búsqueda. */
    public function consultarHistorias($busqueda = null) {
        try {
            $stmt = $this->conn->prepare("call consultar_historia_clinica(null, ?)");
            $stmt->execute([$busqueda]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en consultarHistorias: " . $e->getMessage());
            return [];
        }
    }

    /* Obtiene los datos de una única historia clínica por su ID. */
    public function obtenerHistoriaPorId($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("call consultar_historia_clinica(?, null)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerHistoriaPorId: " . $e->getMessage());
            return null;
        }
    }

    /* Registra una nueva historia clínica en la base de datos. */
    public function registrarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("call registrar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id_paciente'], $datos['id_usuario_administrador'], $datos['estado_salud'], $datos['condiciones'], $datos['antecedentes_medicos'], $datos['alergias'], $datos['dietas_especiales'], $datos['fecha_ultima_consulta'], $datos['observaciones']]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Actualiza la información de una historia clínica existente. */
    public function actualizarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("call actualizar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id_historia_clinica'], $datos['id_usuario_administrador'], $datos['estado_salud'], $datos['condiciones'], $datos['antecedentes_medicos'], $datos['alergias'], $datos['dietas_especiales'], $datos['fecha_ultima_consulta'], $datos['observaciones']]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Desactiva una historia clínica mediante un borrado lógico. */
    public function desactivarHistoria($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("call eliminar_historia_clinica(?)");
            $stmt->execute([$id_historia_clinica]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Obtiene todas las enfermedades asignadas a una historia clínica. */
    public function consultarEnfermedadesAsignadas($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("call consultar_enfermedades_hc(?)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Asigna una enfermedad a una historia clínica. */
    public function asignarEnfermedad($id_hc, $id_enfermedad) {
        try {
            $stmt = $this->conn->prepare("call asignar_enfermedad_hc(?, ?)");
            $stmt->execute([$id_hc, $id_enfermedad]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Elimina la asignación de una enfermedad a una historia clínica. */
    public function eliminarEnfermedadAsignada($id_hc_enfermedad) {
        try {
            $stmt = $this->conn->prepare("call eliminar_enfermedad_hc(?)");
            return $stmt->execute([$id_hc_enfermedad]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Obtiene todos los medicamentos asignados a una historia clínica. */
    public function consultarMedicamentosAsignados($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("call consultar_medicamentos_hc(?)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Asigna un medicamento con su dosis a una historia clínica. */
    public function asignarMedicamento($datos) {
        try {
            $stmt = $this->conn->prepare("call asignar_medicamento_hc(?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id_historia_clinica'], $datos['id_medicamento'], $datos['dosis'], $datos['frecuencia'], $datos['instrucciones']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Actualiza los detalles de un medicamento asignado. */
    public function actualizarMedicamentoAsignado($datos) {
        try {
            $stmt = $this->conn->prepare("call actualizar_medicamento_hc(?, ?, ?, ?)");
            return $stmt->execute([$datos['id_hc_medicamento'], $datos['dosis'], $datos['frecuencia'], $datos['instrucciones']]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Elimina la asignación de un medicamento a una historia clínica. */
    public function eliminarMedicamentoAsignado($id_hc_medicamento) {
        try {
            $stmt = $this->conn->prepare("call eliminar_medicamento_hc(?)");
            return $stmt->execute([$id_hc_medicamento]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /* Obtiene un reporte consolidado de una historia clínica. */
    public function obtenerReporteCompleto($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("call consultar_reporte_completo_hc(?)");
            $stmt->execute([$id_historia_clinica]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerReporteCompleto: " . $e->getMessage());
            return null;
        }
    }

    /* Verifica si un paciente ya tiene una historia clínica activa. */
    public function verificarHcExistente($id_paciente) {
        try {
            $stmt = $this->conn->prepare("select 1 from tb_historia_clinica where id_paciente = ? and estado = 'Activo' limit 1");
            $stmt->execute([$id_paciente]);
            return $stmt->fetchColumn() !== false;
        } catch (Exception $e) {
            error_log("Error en verificarHcExistente: " . $e->getMessage());
            return true; 
        }
    }

    /* Consulta las historias clínicas de los pacientes de un cuidador. */
    public function consultarHistoriasPorCuidador($id_cuidador, $busqueda = null) {
        try {
            $stmt = $this->conn->prepare("call consultar_historias_cuidador(?, ?)");
            $stmt->execute([$id_cuidador, $busqueda]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en consultarHistoriasPorCuidador: " . $e->getMessage());
            return [];
        }
    }
}
?>
