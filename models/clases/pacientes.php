<?php
// archivo: models/clases/paciente.php

class Paciente {
    private $conn;

    public function __construct($db_conn = null) {
        if ($db_conn) {
            $this->conn = $db_conn;
        } else {
            include_once(__DIR__ . '/../../data_base/database.php');
            $db = new Database();
            $this->conn = $db->conectar();
        }
    }

    public function registrar($datos) {
        $stmt = $this->conn->prepare("CALL registrar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $datos['documento_identificacion'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['contacto_emergencia'],
            $datos['estado_civil'],
            $datos['tipo_sangre'],
            $datos['seguro_medico'],
            $datos['numero_seguro'],
            $datos['id_usuario_familiar']
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($datos) {
        $stmt = $this->conn->prepare("CALL actualizar_paciente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $datos['id_paciente'],
            $datos['documento_identificacion'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['contacto_emergencia'],
            $datos['estado_civil'],
            $datos['tipo_sangre'],
            $datos['seguro_medico'],
            $datos['numero_seguro'],
            $datos['id_usuario_familiar'],
            $datos['estado']
        ]);
        return true;
    }

    public function desactivar($id_paciente) {
        $stmt = $this->conn->prepare("CALL desactivar_paciente(?)");
        return $stmt->execute([$id_paciente]);
    }

    public function consultar($filtros) {
        $stmt = $this->conn->prepare("CALL consultar_paciente(?, ?, ?, ?)");
        $stmt->execute([
            $filtros['id_paciente'] ?? null,
            $filtros['documento_identificacion'] ?? null,
            $filtros['nombre'] ?? null,
            $filtros['apellido'] ?? null
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
