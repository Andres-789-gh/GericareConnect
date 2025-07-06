<?php
// controllers/cuidador/historia_clinica.controlador.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se requiere el modelo para poder crear una instancia de él.
require_once __DIR__ . "/../../../models/clases/historia_clinica_modelo.php";

class ControladorHistoriaClinica
{

    // MOSTRAR HISTORIAS CLINICAS
    // El método del controlador puede seguir siendo estático por conveniencia.
    static public function ctrMostrarHistoriasClinicas($item, $valor)
    {
        // Se crea una instancia del modelo.
        $modelo = new ModeloHistoriaClinica();
        // Se llama al método desde el objeto instanciado.
        $respuesta = $modelo->mdlMostrarHistoriasClinicas($item, $valor);
        return $respuesta;
    }

    // CREAR HISTORIA CLINICA
    static public function ctrCrearHistoriaClinica()
    {
        if (isset($_POST["id_paciente"])) {
            
            // quien crea la historia es el administrador logueado.
            $id_admin = $_SESSION["id_usuario"] ?? 0;

            if ($id_admin == 0) {
                echo '<script>
                    alert("Error: Sesión de usuario inválida. Por favor, inicie sesión de nuevo.");
                    window.location = "historia_clinica.php";
                </script>';
                return;
            }

            $datos = array(
                "id_paciente" => $_POST["id_paciente"],
                "id_usuario_administrador" => $id_admin, // Se usa el ID del admin logueado
                "estado_salud" => $_POST["estado_salud"],
                "condiciones" => $_POST["condiciones"],
                "antecedentes_medicos" => $_POST["antecedentes_medicos"],
                "alergias" => $_POST["alergias"],
                "dietas_especiales" => $_POST["dietas_especiales"],
                "observaciones" => $_POST["observaciones"],
                "medicamentos_ids" => $_POST["medicamentos_seleccionados_ids"] ?? "",
                "enfermedades_ids" => $_POST["enfermedades_seleccionadas_ids"] ?? "",
            );
            
            // Se crea una instancia del modelo.
            $modelo = new ModeloHistoriaClinica();
            // Se llama al método desde el objeto instanciado.
            $respuesta = $modelo->mdlCrearHistoriaClinica($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    localStorage.removeItem("historiaClinicaForm");
                    localStorage.removeItem("selected_medicamentos");
                    localStorage.removeItem("selected_enfermedades");
                    alert("Historia Clínica creada correctamente.");
                    window.location = "historia_clinica.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear la historia clínica.");
                    window.location = "historia_clinica.php";
                </script>';
            }
        }
    }

    // EDITAR HISTORIA CLINICA
    static public function ctrEditarHistoriaClinica()
    {
        if (isset($_POST["id_historia_clinica_editar"])) {

            $datos = array(
                "id_historia_clinica"   => $_POST["id_historia_clinica_editar"],
                "estado_salud"          => $_POST["estado_salud"],
                "condiciones"           => $_POST["condiciones"],
                "antecedentes_medicos"  => $_POST["antecedentes_medicos"],
                "alergias"              => $_POST["alergias"],
                "dietas_especiales"     => $_POST["dietas_especiales"],
                "observaciones"         => $_POST["observaciones"],
                "medicamentos_ids"      => $_POST["medicamentos_seleccionados_ids"] ?? "",
                "enfermedades_ids"      => $_POST["enfermedades_seleccionadas_ids"] ?? ""
            );

            // Se crea una instancia del modelo.
            $modelo = new ModeloHistoriaClinica();
            // Se llama al método desde el objeto instanciado.
            $respuesta = $modelo->mdlEditarHistoriaClinica($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Historia Clínica actualizada correctamente.");
                    window.location = "historia_clinica.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al actualizar la historia clínica.");
                    window.location = "historia_clinica.php?idHistoriaClinica=' . $_POST["id_historia_clinica_editar"] . '";
                </script>';
            }
        }
    }

    // ELIMINAR HISTORIA CLINICA
    static public function ctrEliminarHistoriaClinica()
    {
        if (isset($_GET["idHistoriaClinica"])) {
            $idHistoria = $_GET["idHistoriaClinica"];
            
            // Se crea una instancia del modelo.
            $modelo = new ModeloHistoriaClinica();
            // Se llama al método desde el objeto instanciado.
            $respuesta = $modelo->mdlDesactivarHistoriaClinica($idHistoria);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Historia clínica eliminada correctamente.");
                    window.location = "historia_clinica.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al eliminar la historia clínica.");
                    window.location = "historia_clinica.php";
                </script>';
            }
        }
    }
}