<?php
class Paciente {
    private $conn;

    // Se conecta a la BD usando el método original de tu proyecto.
    public function __construct() {
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

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
     * Obtiene los datos de un único paciente por su ID 
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
     * Registra un nuevo paciente en la base de datos
     */
    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['documento_identificacion'], $datos['nombre'], $datos['apellido'],
                $datos['fecha_nacimiento'], $datos['genero'], $datos['contacto_emergencia'],
                $datos['estado_civil'], $datos['tipo_sangre'], $datos['seguro_medico'],
                $datos['numero_seguro'], $datos['id_usuario_familiar'],
                $datos['id_usuario_cuidador'], $datos['id_usuario_administrador'],
                $datos['descripcion_asignacion']
            ]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al registrar paciente: " . $e->getMessage());
        }
    }

    /**
     * Actualiza los datos de un paciente existente 
     */
    public function actualizar($datos) {
        try {
            $query = $this->conn->prepare("CALL actualizar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['id_paciente'], $datos['documento_identificacion'], $datos['nombre'],
                $datos['apellido'], $datos['fecha_nacimiento'], $datos['genero'],
                $datos['contacto_emergencia'], $datos['estado_civil'], $datos['tipo_sangre'],
                $datos['seguro_medico'], $datos['numero_seguro'],
                $datos['id_usuario_familiar'], $datos['id_usuario_cuidador'],
                $datos['id_usuario_administrador'], $datos['descripcion_asignacion']
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar paciente: " . $e->getMessage());
        }
    }

    public function desactivar($id_paciente) {
        try {
            $query = $this->conn->prepare("CALL desactivar_paciente(?)");
            $query->execute([$id_paciente]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al desactivar paciente: " . $e->getMessage());
        }
    }

    public function consultarPacientesActivos()
    {
        try {
            // Llama al procedimiento almacenado usando PDO
            $stmt = $this->conn->prepare("CALL ConsultarPacientesActivos()");
            $stmt->execute();
            // Devuelve un array con los pacientes activos
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Es una buena práctica manejar posibles excepciones.
            // Aquí se podría registrar el error para depuración.
            error_log("Error en consultarPacientesActivos: " . $e->getMessage());
            return []; // Devuelve un array vacío en caso de error.
        }
    }

    public function obtenerAsignacionActiva($id_paciente) {
        try {
            $query = $this->conn->prepare("SELECT id_usuario_cuidador, descripcion FROM tb_paciente_asignado WHERE id_paciente = ? AND estado = 'Activo' LIMIT 1");
            $query->execute([$id_paciente]);
            return $query->fetch(PDO::FETCH_ASSOC); // Devuelve un array asociativo o false
        } catch (Exception $e) {
            return null;
        }
    }
}
?>