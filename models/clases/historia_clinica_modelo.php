<?php
/**
 * Modelo para gestionar las operaciones de la Historia Clínica en la base de datos.
 * Se conecta a la base de datos y utiliza los procedimientos almacenados definidos
 * en 'procedimientos_historia_clinica.sql'.
 */
class ModeloHistoriaClinica {

    // Propiedad para guardar la conexión a la BD
    protected $conn;

    /**
     * Constructor de la clase.
     * Se ejecuta al crear un nuevo objeto e inicializa la conexión a la base de datos.
     */
    public function __construct() {
        // Se utiliza __DIR__ para asegurar que la ruta al archivo de la base de datos sea siempre la correcta.
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    public function mdlRegistrarHistoriaClinica($datos) {
        try {
            $stmt = $this->conn->prepare("CALL registrar_historia_clinica(:id_paciente, :id_usuario_administrador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :fecha_ultima_consulta, :observaciones)");

            $stmt->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario_administrador", $datos["id_usuario_administrador"], PDO::PARAM_INT);
            $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
            $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
            $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
            $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
            $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_ultima_consulta", $datos["fecha_ultima_consulta"], PDO::PARAM_STR);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            // Log del error para depuración
            error_log("Error en mdlRegistrarHistoriaClinica: " . $e->getMessage());
            return "error";
        }
    }

    public function mdlConsultarHistoriaClinica($item, $valor) {
        try {
            if ($item != null) {
                // Prepara la llamada para buscar por un item específico
                $stmt = $this->conn->prepare("CALL consultar_historia_clinica(:" . $item . ", NULL)");
                if ($item == "id_historia_clinica") {
                    $stmt->bindParam(":id_historia_clinica", $valor, PDO::PARAM_INT);
                } elseif ($item == "id_paciente") {
                    $stmt = $this->conn->prepare("CALL consultar_historia_clinica(NULL, :id_paciente)");
                    $stmt->bindParam(":id_paciente", $valor, PDO::PARAM_INT);
                }
            } else {
                return [];
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en mdlConsultarHistoriaClinica: " . $e->getMessage());
            return false;
        }
    }

    public function mdlActualizarHistoriaClinica($datos) {
        try {
            $stmt = $this->conn->prepare("CALL actualizar_historia_clinica(:id_historia_clinica, :id_usuario_administrador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :fecha_ultima_consulta, :observaciones, :estado)");

            $stmt->bindParam(":id_historia_clinica", $datos["id_historia_clinica"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario_administrador", $datos["id_usuario_administrador"], PDO::PARAM_INT);
            $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
            $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
            $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
            $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
            $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_ultima_consulta", $datos["fecha_ultima_consulta"], PDO::PARAM_STR);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // ej. 'Activo' o 'Inactivo'

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Error en mdlActualizarHistoriaClinica: " . $e->getMessage());
            return "error";
        }
    }

    public function mdlEliminarHistoriaClinica($idHistoria) {
        try {
            $stmt = $this->conn->prepare("CALL eliminar_historia_clinica(:id_historia_clinica)");
            $stmt->bindParam(":id_historia_clinica", $idHistoria, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Error en mdlEliminarHistoriaClinica: " . $e->getMessage());
            return "error";
        }
    }
}
?>