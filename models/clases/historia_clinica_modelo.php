<?php
class ModeloHistoriaClinica{
    // Propiedad para guardar la conexión a la BD
    protected $conn;

    // El constructor se ejecuta al crear un nuevo objeto.
    // Incluye el archivo de la base de datos y asigna la conexión a la propiedad de la clase.
    public function __construct() {
        // Se utiliza __DIR__ para asegurar que la ruta sea siempre la correcta.
        require(__DIR__ . '/../data_base/database.php');
        $this->conn = $conn;
    }

    // MOSTRAR HISTORIAS CLINICAS
    public function mdlMostrarHistoriasClinicas($item, $valor)
    {
        if ($item != null) {
            $stmt = $this->conn->prepare("CALL obtener_historia_clinica_por_id(:id_historia_clinica)");
            $stmt->bindParam(":id_historia_clinica", $valor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = $this->conn->prepare("CALL mostrar_historias_clinicas()");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    // CREAR HISTORIA CLINICA
    public function mdlCrearHistoriaClinica($datos)
    {
        $stmt = $this->conn->prepare("CALL crear_historia_clinica(:id_paciente, :id_usuario_administrador, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :observaciones, :medicamentos_ids, :enfermedades_ids)");

        $stmt->bindParam(":id_paciente", $datos["id_paciente"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario_administrador", $datos["id_usuario_administrador"], PDO::PARAM_INT);
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":medicamentos_ids", $datos["medicamentos_ids"], PDO::PARAM_STR);
        $stmt->bindParam(":enfermedades_ids", $datos["enfermedades_ids"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            error_log(print_r($stmt->errorInfo(), true));
            return "error";
        }
    }
    
    // EDITAR HISTORIA CLINICA
    public function mdlEditarHistoriaClinica($datos)
    {
        $stmt = $this->conn->prepare("CALL actualizar_historia_clinica_completa(:id_historia_clinica, :estado_salud, :condiciones, :antecedentes_medicos, :alergias, :dietas_especiales, :observaciones, :medicamentos_ids, :enfermedades_ids)");

        $stmt->bindParam(":id_historia_clinica", $datos["id_historia_clinica"], PDO::PARAM_INT);
        $stmt->bindParam(":estado_salud", $datos["estado_salud"], PDO::PARAM_STR);
        $stmt->bindParam(":condiciones", $datos["condiciones"], PDO::PARAM_STR);
        $stmt->bindParam(":antecedentes_medicos", $datos["antecedentes_medicos"], PDO::PARAM_STR);
        $stmt->bindParam(":alergias", $datos["alergias"], PDO::PARAM_STR);
        $stmt->bindParam(":dietas_especiales", $datos["dietas_especiales"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":medicamentos_ids", $datos["medicamentos_ids"], PDO::PARAM_STR);
        $stmt->bindParam(":enfermedades_ids", $datos["enfermedades_ids"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } catch (PDOException $e) { return "error"; }
    }

    // DESACTIVAR HISTORIA CLINICA
    public function mdlDesactivarHistoriaClinica($idHistoria)
    {
        $stmt = $this->conn->prepare("CALL desactivar_historia_clinica(:id_historia_clinica)");
        $stmt->bindParam(":id_historia_clinica", $idHistoria, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } catch (PDOException $e) { return "error"; }
    }
    
    // MÉTODOS AUXILIARES
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