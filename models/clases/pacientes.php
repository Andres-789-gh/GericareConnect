<?php
require_once __DIR__ . '/../data_base/database.php';

class Paciente {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

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
            throw new Exception("Error al registrar paciente: " . $e->getMessage());
        }
    }
}
?>
