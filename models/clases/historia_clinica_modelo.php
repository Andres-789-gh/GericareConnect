<?php

class ModeloHistoriaClinica
{
    // Propiedad para guardar la conexión a la BD
    protected $conn;

    // El constructor se ejecuta al crear un nuevo objeto.
    public function __construct()
    {
        // Se utiliza __DIR__ para asegurar que la ruta sea siempre la correcta.
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    public function MostrarHistoriaClinica($id_historia_clinica, $id_paciente)
    {
        $stmt = $this->conn->prepare("CALL consultar_historia_clinica(:id_hc, :id_p)");
        
        $stmt->bindParam(":id_hc", $id_historia_clinica, PDO::PARAM_INT);
        $stmt->bindParam(":id_p", $id_paciente, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function CrearHistoriaClinica($datos)
    {
        // Se llama al procedimiento que creamos, que no maneja tablas intermedias.
        $stmt = $this->conn->prepare("CALL registrar_historia_clinica(:id_paciente, :id_usuario_administrador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :fecha_ultima_consulta, :observaciones)");

        $stmt->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario_administrador", $datos["id_usuario_administrador"], PDO::PARAM_INT);
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_ultima_consulta", $datos["fecha_ultima_consulta"], PDO::PARAM_STR); // Parámetro requerido por el SP
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            error_log(print_r($stmt->errorInfo(), true));
            return "error";
        }
    }

    public function EditarHistoriaClinica($datos)
    {
        // Se llama al procedimiento que creamos, que no maneja tablas intermedias.
        $stmt = $this->conn->prepare("CALL actualizar_historia_clinica(:id_historia_clinica, :id_usuario_administrador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :fecha_ultima_consulta, :observaciones, :estado)");

        $stmt->bindParam(":id_historia_clinica", $datos["id_historia_clinica"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario_administrador", $datos["id_usuario_administrador"], PDO::PARAM_INT); // Parámetro requerido por el SP
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_ultima_consulta", $datos["fecha_ultima_consulta"], PDO::PARAM_STR); // Parámetro requerido por el SP
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR); // Parámetro requerido por el SP

        if ($stmt->execute()) {
            return "ok";
        } else {
            error_log(print_r($stmt->errorInfo(), true));
            return "error";
        }
    }

    public function DesactivarHistoriaClinica($idHistoria)
    {
        $stmt = $this->conn->prepare("CALL eliminar_historia_clinica(:id_historia_clinica)");
        $stmt->bindParam(":id_historia_clinica", $idHistoria, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
    
    // --- MÉTODOS AUXILIARES (sin cambios, pero requieren sus propios SP o queries) ---

    public function mdlObtenerPacientesActivos()
    {
        $stmt = $this->conn->prepare("SELECT id_paciente, nombre, apellido FROM tb_paciente WHERE estado = 'Activo' ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // faltan los sp
    public function mdlMostrarMedicamentosPorHistoria($id_historia_clinica) {
        $stmt = $this->conn->prepare("CALL obtener_medicamentos_por_historia(:id_historia_clinica)");
        $stmt->bindParam(":id_historia_clinica", $id_historia_clinica, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mdlMostrarEnfermedadesPorHistoria($id_historia_clinica) {
        $stmt = $this->conn->prepare("CALL obtener_enfermedades_por_historia(:id_historia_clinica)");
        $stmt->bindParam(":id_historia_clinica", $id_historia_clinica, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}