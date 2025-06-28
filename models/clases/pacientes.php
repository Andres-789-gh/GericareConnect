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
        // Incluye el archivo de conexión. __DIR__ asegura que la ruta es correcta.
        include_once(__DIR__ . '/../../data_base/database.php');
        
        // Asigna la conexión a la propiedad de la clase.
        $this->conn = $conn;
    }
    
    /**
     * Registra un nuevo paciente llamando al procedimiento almacenado.
     *
     * @param array $datos Los datos del paciente a registrar.
     * @return mixed El resultado del procedimiento almacenado.
     * @throws Exception Si ocurre un error.
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
            // Relanza la excepción para un manejo de errores centralizado.
            throw $e;
        }
    }

    // ... (Aquí irían tus otros métodos: actualizar, consultar, desactivar) ...
}
?>
