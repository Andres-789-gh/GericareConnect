<?php

/**
 * Clase para gestionar las operaciones de los pacientes en la base de datos.
 */
class Paciente {
    /**
     * @var PDO La conexión a la base de datos.
     */
    protected $conn;

    /**
     * Constructor de la clase. Inicia la conexión a la base de datos.
     */
    public function __construct() {
        // Se incluye el archivo donde se encuentra la conexión.
        // __DIR__ asegura que la ruta parte desde la carpeta actual del archivo.
        include(__DIR__ . '/../data_base/database.php');
        
        // Asigna la conexión ($conn) a la propiedad protegida "$this->conn" del objeto actual.
        $this->conn = $conn;
    }
    
    /**
     * Registra un nuevo paciente en la base de datos llamando a un procedimiento almacenado.
     *
     * @param array $datos Los datos del paciente a registrar.
     * @return mixed El resultado del procedimiento almacenado (ej. el nuevo ID).
     * @throws Exception Si ocurre un error durante la ejecución.
     */
    public function registrar($datos) {
        try {
            $query = $this->conn->prepare("CALL registrar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $query->bindParam(1,  $datos['documento_identificacion']);
            $query->bindParam(2,  $datos['nombre']);
            $query->bindParam(3,  $datos['apellido']);
            $query->bindParam(4,  $datos['fecha_nacimiento']);
            $query->bindParam(5,  $datos['genero']);
            $query->bindParam(6,  $datos['contacto_emergencia']);
            $query->bindParam(7,  $datos['estado_civil']);
            $query->bindParam(8,  $datos['tipo_sangre']);
            $query->bindParam(9,  $datos['seguro_medico']);
            $query->bindParam(10, $datos['numero_seguro']);
            $query->bindParam(11, $datos['id_usuario_familiar']);

            $query->execute();

            return $query->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            // Relanza la excepción para que sea manejada por el código superior.
            throw $e;
        }
    }

    /**
     * Actualiza los datos de un paciente existente.
     *
     * @param array $datos Los nuevos datos del paciente.
     * @return bool Retorna true si la actualización fue exitosa.
     * @throws Exception Si ocurre un error durante la ejecución.
     */
    public function actualizar($datos) {
        try {
            $query = $this->conn->prepare("CALL actualizar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $query->bindParam(1,  $datos['id_paciente']);
            $query->bindParam(2,  $datos['documento_identificacion']);
            $query->bindParam(3,  $datos['nombre']);
            $query->bindParam(4,  $datos['apellido']);
            $query->bindParam(5,  $datos['fecha_nacimiento']);
            $query->bindParam(6,  $datos['genero']);
            $query->bindParam(7,  $datos['contacto_emergencia']);
            $query->bindParam(8,  $datos['estado_civil']);
            $query->bindParam(9,  $datos['tipo_sangre']);
            $query->bindParam(10, $datos['seguro_medico']);
            $query->bindParam(11, $datos['numero_seguro']);
            $query->bindParam(12, $datos['id_usuario_familiar']);
            $query->bindParam(13, $datos['estado']);

            $query->execute();
            return true;

        } catch (Exception $e) {
            // Relanza la excepción para que sea manejada por el código superior.
            throw $e;
        }
    }

    /**
     * Consulta pacientes basados en filtros.
     *
     * @param array $filtros Los criterios de búsqueda (id_paciente, documento, nombre, apellido).
     * @return array Un arreglo de pacientes que coinciden con los filtros.
     * @throws Exception Si ocurre un error durante la ejecución.
     */
    public function consultar($filtros) {
        try {
            $query = $this->conn->prepare("CALL consultar_paciente(?, ?, ?, ?)");

            $id_paciente = $filtros['id_paciente'] ?? null;
            $documento   = $filtros['documento_identificacion'] ?? null;
            $nombre      = $filtros['nombre'] ?? null;
            $apellido    = $filtros['apellido'] ?? null;

            $query->bindParam(1, $id_paciente);
            $query->bindParam(2, $documento);
            $query->bindParam(3, $nombre);
            $query->bindParam(4, $apellido);

            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            // Relanza la excepción para que sea manejada por el código superior.
            throw $e;
        }
    }

    /**
     * Desactiva un paciente (baja lógica).
     *
     * @param int $id_paciente El ID del paciente a desactivar.
     * @return bool Retorna true si la desactivación fue exitosa.
     * @throws Exception Si ocurre un error durante la ejecución.
     */
    public function desactivar($id_paciente) {
        try {
            $query = $this->conn->prepare("CALL desactivar_paciente(?)");
            $query->bindParam(1, $id_paciente, PDO::PARAM_INT);
            $query->execute();
            return true;
            
        } catch (Exception $e) {
            // Relanza la excepción para que sea manejada por el código superior.
            throw $e;
        }
    }
}
?>