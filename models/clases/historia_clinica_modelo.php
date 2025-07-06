<?php
require_once __DIR__ . '/../../data_base/database.php';

class HistoriaClinica
{
    // ... (propiedades existentes: id_historia_clinica, id_paciente, etc.)
    private $id_historia_clinica;
    private $id_paciente;
    private $fecha_creacion;
    private $fecha_actualizacion;
    private $motivo_consulta;
    private $antecedentes_medicos;
    private $examen_fisico;
    private $diagnostico;
    private $plan_tratamiento;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConn();
    }

    // ... (setters y getters existentes)
    public function setIdHistoriaClinica($id) { $this->id_historia_clinica = $id; }
    public function setIdPaciente($id) { $this->id_paciente = $id; }
    public function setFechaCreacion($fecha) { $this->fecha_creacion = $fecha; }
    public function setFechaActualizacion($fecha) { $this->fecha_actualizacion = $fecha; }
    public function setMotivoConsulta($motivo) { $this->motivo_consulta = $motivo; }
    public function setAntecedentesMedicos($antecedentes) { $this->antecedentes_medicos = $antecedentes; }
    public function setExamenFisico($examen) { $this->examen_fisico = $examen; }
    public function setDiagnostico($diagnostico) { $this->diagnostico = $diagnostico; }
    public function setPlanTratamiento($plan) { $this->plan_tratamiento = $plan; }


    public function registrarHistoriaClinica()
    {
        $stmt = $this->db->prepare("CALL RegistrarHistoriaClinica(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issssss",
            $this->id_paciente,
            $this->fecha_creacion,
            $this->motivo_consulta,
            $this->antecedentes_medicos,
            $this->examen_fisico,
            $this->diagnostico,
            $this->plan_tratamiento
        );
        return $stmt->execute();
    }

    public function editarHistoriaClinica()
    {
        $stmt = $this->db->prepare("CALL EditarHistoriaClinica(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issssss",
            $this->id_historia_clinica,
            $this->fecha_creacion,
            $this->motivo_consulta,
            $this->antecedentes_medicos,
            $this->examen_fisico,
            $this->diagnostico,
            $this->plan_tratamiento
        );
        return $stmt->execute();
    }

    public function consultarHistoriasClinicas()
    {
        $stmt = $this->db->prepare("CALL ConsultarHistoriasClinicas()");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerHistoriaClinicaPorId($id)
    {
        $stmt = $this->db->prepare("CALL ObtenerHistoriaClinicaPorID(?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    // --- NUEVA FUNCIÓN ---
    public function buscarHistoriasClinicas($term)
    {
        $stmt = $this->db->prepare("CALL BuscarHistoriasClinicas(?)");
        $likeTerm = "%" . $term . "%";
        $stmt->bind_param("s", $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
