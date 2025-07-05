<?php
class Paciente {
    private $conn;

    // Se conecta a la BD usando el método original de tu proyecto.
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    /**
     * CORRECCIÓN FINAL: Consulta todos los pacientes activos.
     * Ahora llama al procedimiento SIN enviar parámetros.
     */
    public function consultar() {
        try {
            $query = $this->conn->prepare("CALL consultar_pacientes()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al consultar pacientes: " . $e->getMessage());
        }
    }

    /**
     * Obtiene los datos de un único paciente por su ID para el modo de edición.
     */
    public function obtenerPorId($id_paciente) {
        try {
            $query = $this->conn->prepare("SELECT * FROM tb_paciente WHERE id_paciente = ?");
            $query->execute([$id_paciente]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener paciente por ID: " . $e->getMessage());
        }
    }

    /**
     * Registra un nuevo paciente en la base de datos (SIN alergias).
     */
    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['documento_identificacion'], $datos['nombre'], $datos['apellido'],
                $datos['fecha_nacimiento'], $datos['genero'], $datos['contacto_emergencia'],
                $datos['estado_civil'], $datos['tipo_sangre'], $datos['seguro_medico'],
                $datos['numero_seguro'], $datos['id_usuario_familiar']
            ]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al registrar paciente: " . $e->getMessage());
        }
    }

    /**
     * Actualiza los datos de un paciente existente (SIN alergias).
     */
    public function actualizar($datos) {
        try {
            $query = $this->conn->prepare("CALL actualizar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['id_paciente'], $datos['documento_identificacion'], $datos['nombre'],
                $datos['apellido'], $datos['fecha_nacimiento'], $datos['genero'],
                $datos['contacto_emergencia'], $datos['estado_civil'], $datos['tipo_sangre'],
                $datos['seguro_medico'], $datos['numero_seguro'],
                $datos['id_usuario_familiar']
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar paciente: " . $e->getMessage());
        }
    }

    /**
     * Desactiva un paciente (borrado lógico).
     */
    public function desactivar($id_paciente) {
        try {
            $query = $this->conn->prepare("CALL desactivar_paciente(?)");
            $query->execute([$id_paciente]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al desactivar paciente: " . $e->getMessage());
        }
    }
}
?>
