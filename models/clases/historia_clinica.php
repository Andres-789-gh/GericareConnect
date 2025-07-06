<?php
require_once __DIR__ . '/../data_base/database.php';

class HistoriaClinica {
    private $conn;

    public function __construct() {
        // La variable $conn viene del archivo database.php incluido
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Consulta las historias clínicas para la vista principal.
     * Acepta un término de búsqueda para filtrar.
     */
    public function consultarHistorias($busqueda = null) {
        try {
            $stmt = $this->conn->prepare("CALL consultar_historia_clinica(NULL, ?)");
            $stmt->execute([$busqueda]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error en consultarHistorias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los datos de una única historia clínica por su ID para la edición.
     */
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

    /**
     * Registra una nueva historia clínica en la base de datos.
     */
    public function registrarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("CALL registrar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['id_paciente'],
                $datos['id_usuario_administrador'],
                $datos['estado_salud'],
                $datos['condiciones'],
                $datos['antecedentes_medicos'],
                $datos['alergias'],
                $datos['dietas_especiales'],
                $datos['fecha_ultima_consulta'],
                $datos['observaciones']
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error en registrarHistoria: " . $e->getMessage());
            throw $e; // Relanzar la excepción para que el controlador la maneje
        }
    }

    /**
     * Actualiza los datos de una historia clínica existente.
     */
    public function actualizarHistoria($datos) {
        try {
            $stmt = $this->conn->prepare("CALL actualizar_historia_clinica(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['id_historia_clinica'],
                $datos['id_usuario_administrador'],
                $datos['estado_salud'],
                $datos['condiciones'],
                $datos['antecedentes_medicos'],
                $datos['alergias'],
                $datos['dietas_especiales'],
                $datos['fecha_ultima_consulta'],
                $datos['observaciones']
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarHistoria: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Desactiva una historia clínica (borrado lógico).
     */
    public function desactivarHistoria($id_historia_clinica) {
        try {
            $stmt = $this->conn->prepare("CALL eliminar_historia_clinica(?)");
            $stmt->execute([$id_historia_clinica]);
            return true;
        } catch (Exception $e) {
            error_log("Error en desactivarHistoria: " . $e->getMessage());
            throw $e;
        }
    }
}
?>