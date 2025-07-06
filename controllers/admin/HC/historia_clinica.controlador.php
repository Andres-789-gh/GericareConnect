<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../../models/clases/historia_clinica_modelo.php";

class ControladorHistoriaClinica {
    static public function ctrMostrarHistoriasClinicas($item, $valor) {
        $modelo = new ModeloHistoriaClinica();
        return $modelo->mdlMostrarHistoriasClinicas($item, $valor);
    }

    static public function ctrCrearHistoriaClinica() {
        if (isset($_POST["id_paciente"])) {
            $id_admin = $_SESSION["id_usuario"] ?? 0;
            if ($id_admin == 0) {
                echo '<script>alert("Error: Sesión de administrador inválida."); window.history.back();</script>'; return;
            }
            $datos = ["id_paciente" => $_POST["id_paciente"], "id_usuario_administrador" => $id_admin, "estado_salud" => trim($_POST["estado_salud"]), "condiciones" => trim($_POST["condiciones"]), "antecedentes_medicos" => trim($_POST["antecedentes_medicos"]), "alergias" => trim($_POST["alergias"]), "dietas_especiales" => trim($_POST["dietas_especiales"]), "observaciones" => trim($_POST["observaciones"])];
            $modelo = new ModeloHistoriaClinica();
            $respuesta = $modelo->mdlCrearHistoriaBase($datos);
            if ($respuesta && isset($respuesta['id_historia_clinica_creada'])) {
                $id_creado = $respuesta['id_historia_clinica_creada'];
                echo "<script>alert('Historia Clínica creada. Ahora puede asignar medicamentos y enfermedades.'); window.location = 'editar_historia_clinica.php?idHistoriaClinica={$id_creado}';</script>";
            } else {
                echo '<script>alert("Error al crear la historia clínica."); window.history.back();</script>';
            }
        }
    }

    static public function ctrEditarHistoriaClinica() {
        if (isset($_POST["id_historia_clinica_editar"])) {
            $datos = ["id_historia_clinica" => $_POST["id_historia_clinica_editar"], "estado_salud" => trim($_POST["estado_salud"]), "condiciones" => trim($_POST["condiciones"]), "antecedentes_medicos" => trim($_POST["antecedentes_medicos"]), "alergias" => trim($_POST["alergias"]), "dietas_especiales" => trim($_POST["dietas_especiales"]), "observaciones" => trim($_POST["observaciones"]), "medicamentos_ids" => $_POST["medicamentos_seleccionados_ids"] ?? "", "enfermedades_ids" => $_POST["enfermedades_seleccionados_ids"] ?? ""];
            $modelo = new ModeloHistoriaClinica();
            if ($modelo->mdlEditarHistoriaClinica($datos) == "ok") {
                echo '<script>alert("Historia Clínica actualizada correctamente."); window.location = "historia_clinica.php";</script>';
            } else {
                echo '<script>alert("Error al actualizar la historia clínica."); window.location = "editar_historia_clinica.php?idHistoriaClinica=' . $_POST["id_historia_clinica_editar"] . '";</script>';
            }
        }
    }

    static public function ctrEliminarHistoriaClinica() {
        if (isset($_GET["idHistoriaClinica"])) {
            $modelo = new ModeloHistoriaClinica();
            if ($modelo->mdlDesactivarHistoriaClinica($_GET["idHistoriaClinica"]) == "ok") {
                echo '<script>alert("Historia clínica eliminada."); window.location = "historia_clinica.php";</script>';
            } else {
                echo '<script>alert("Error al eliminar."); window.location = "historia_clinica.php";</script>';
            }
        }
    }
}