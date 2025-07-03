<?php
// Incluye el archivo de conexión a la base de datos una sola vez.
require_once __DIR__ . '/../data_base/database.php';

class Paciente {
    private $conn; // Propiedad para guardar la conexión a la BD

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // Método para OBTENER (Leer) todos los pacientes.
    public function consultar($busqueda = null) {
        try {
            $query = $this->conn->prepare("CALL consultar_pacientes(?)");
            $query->bindParam(1, $busqueda, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al consultar pacientes: " . $e->getMessage());
        }
    }

    // Método para obtener un ÚNICO paciente por su ID (para editar).
    public function obtenerPorId($id_paciente) {
        try {
            // Este método no necesita un procedimiento almacenado, es una consulta simple.
            $query = $this->conn->prepare("SELECT * FROM tb_paciente WHERE id_paciente = ?");
            $query->execute([$id_paciente]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener paciente por ID: " . $e->getMessage());
        }
    }

    // --- CORRECCIÓN AQUÍ ---
    // Método para CREAR un nuevo paciente, ahora con 12 parámetros.
    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            // Vincula los 12 datos del formulario al procedimiento almacenado.
            $query->execute([
                $datos['documento_identificacion'], $datos['nombre'], $datos['apellido'],
                $datos['fecha_nacimiento'], $datos['genero'], $datos['contacto_emergencia'],
                $datos['estado_civil'], $datos['tipo_sangre'], $datos['seguro_medico'],
                $datos['numero_seguro'], $datos['alergias'], $datos['id_usuario_familiar']
            ]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al registrar paciente: " . $e->getMessage());
        }
    }

    // --- CORRECCIÓN AQUÍ ---
    // Método para ACTUALIZAR un paciente, ahora con 12 parámetros.
    public function actualizar($datos) {
        try {
            $query = $this->conn->prepare("CALL actualizar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $datos['id_paciente'], $datos['documento_identificacion'], $datos['nombre'],
                $datos['apellido'], $datos['fecha_nacimiento'], $datos['genero'],
                $datos['contacto_emergencia'], $datos['estado_civil'], $datos['tipo_sangre'],
                $datos['seguro_medico'], $datos['numero_seguro'], $datos['alergias'],
                $datos['id_usuario_familiar']
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar paciente: " . $e->getMessage());
        }
    }

    // Método para ELIMINAR (desactivar) un paciente.
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