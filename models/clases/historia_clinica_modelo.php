<?php
// La conexión se manejará en el constructor, como en tus otros modelos.

class HistoriaClinica
{
    // Propiedad para la conexión PDO, consistente con tu proyecto.
    private $conn;

    // Propiedades del modelo, ajustadas a tu tabla tb_historia_clinica
    private $id_historia_clinica;
    private $id_paciente;
    private $id_usuario_administrador;
    private $estado_salud;
    private $condiciones;
    private $antecedentes_medicos;
    private $alergias;
    private $dietas_especiales;
    private $fecha_ultima_consulta;
    private $observaciones;
    private $estado;


    /**
     * Constructor que establece la conexión a la base de datos
     * usando el método consistente con el resto del proyecto.
     */
    public function __construct()
    {
        // Se utiliza el método de conexión original de tu proyecto.
        require_once __DIR__ . '/../data_base/database.php';
        // Asigna la conexión PDO a la propiedad de la clase
        $this->conn = $conn;
    }

    // --- SETTERS (Ajustados a tu tabla) ---
    public function setIdHistoriaClinica($id) { $this->id_historia_clinica = $id; }
    public function setIdPaciente($id) { $this->id_paciente = $id; }
    public function setIdUsuarioAdministrador($id) { $this->id_usuario_administrador = $id; }
    public function setEstadoSalud($texto) { $this->estado_salud = $texto; }
    public function setCondiciones($texto) { $this->condiciones = $texto; }
    public function setAntecedentesMedicos($texto) { $this->antecedentes_medicos = $texto; }
    public function setAlergias($texto) { $this->alergias = $texto; }
    public function setDietasEspeciales($texto) { $this->dietas_especiales = $texto; }
    public function setFechaUltimaConsulta($fecha) { $this->fecha_ultima_consulta = $fecha; }
    public function setObservaciones($texto) { $this->observaciones = $texto; }
    public function setEstado($estado) { $this->estado = $estado; }


    // --- MÉTODOS DE LA CLASE (ADAPTADOS A PDO Y A TU ESTRUCTURA) ---

    /**
     * Se asume que existe un procedimiento almacenado 'RegistrarHistoriaClinica'
     * que coincide con los campos de tu tabla.
     */
    public function registrarHistoriaClinica()
    {
        try {
            $stmt = $this->conn->prepare("CALL RegistrarHistoriaClinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $this->id_paciente,
                $this->id_usuario_administrador,
                $this->estado_salud,
                $this->condiciones,
                $this->antecedentes_medicos,
                $this->alergias,
                $this->dietas_especiales,
                $this->fecha_ultima_consulta,
                $this->observaciones
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en registrarHistoriaClinica: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Se asume que existe un procedimiento almacenado 'EditarHistoriaClinica'
     * que coincide con los campos de tu tabla.
     */
    public function editarHistoriaClinica()
    {
        try {
            $stmt = $this->conn->prepare("CALL EditarHistoriaClinica(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $this->id_historia_clinica,
                $this->id_paciente, // Generalmente el paciente no se edita, pero se incluye si el SP lo requiere
                $this->id_usuario_administrador,
                $this->estado_salud,
                $this->condiciones,
                $this->antecedentes_medicos,
                $this->alergias,
                $this->dietas_especiales,
                $this->fecha_ultima_consulta,
                $this->observaciones
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en editarHistoriaClinica: " . $e->getMessage());
            return false;
        }
    }

    public function consultarHistoriasClinicas()
    {
        try {
            // Este SP debe devolver los campos correctos de tu tabla
            $stmt = $this->conn->prepare("CALL ConsultarHistoriasClinicas()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en consultarHistoriasClinicas: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerHistoriaClinicaPorId($id)
    {
        try {
            // Este SP debe devolver los campos correctos de tu tabla
            $stmt = $this->conn->prepare("CALL ObtenerHistoriaClinicaPorID(?)");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerHistoriaClinicaPorId: " . $e->getMessage());
            return null;
        }
    }
    
    public function buscarHistoriasClinicas($term)
    {
        try {
            // Este SP debe buscar en los campos correctos (nombre, apellido, documento del paciente)
            $stmt = $this->conn->prepare("CALL BuscarHistoriasClinicas(?)");
            $likeTerm = "%" . $term . "%";
            $stmt->execute([$likeTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en buscarHistoriasClinicas: " . $e->getMessage());
            return [];
        }
    }
}
