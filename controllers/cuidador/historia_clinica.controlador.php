<?php
// controllers/cuidador/historia_clinica.controlador.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ControladorHistoriaClinica
{

    /*=============================================
    MOSTRAR HISTORIAS CLINICAS
    =============================================*/
    static public function ctrMostrarHistoriasClinicas($item, $valor)
    {
        $tabla = "tb_historia_clinica";
        $respuesta = ModeloHistoriaClinica::mdlMostrarHistoriasClinicas($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR HISTORIA CLINICA
    =============================================*/
    static public function ctrCrearHistoriaClinica()
    {
        if (isset($_POST["id_paciente"])) {
            $medicamentos_ids = $_POST["medicamentos_seleccionados_ids"] ?? "";
            $enfermedades_ids = $_POST["enfermedades_seleccionadas_ids"] ?? "";

            // --- INICIO DE MODIFICACIÓN PARA PRUEBAS ---
            
            // 1. Comentamos la línea que lee la sesión
            // $id_cuidador = $_SESSION["id_usuario"] ?? 0;

            // 2. Asignamos un ID de cuidador fijo para las pruebas. 
            //    (El usuario con ID 2 es 'Luis Pérez', el cuidador de ejemplo)
            $id_cuidador = 2; 

            // --- FIN DE MODIFICACIÓN PARA PRUEBAS ---


            // Se añade una validación para asegurar que el ID del cuidador es válido
            if ($id_cuidador == 0) {
                echo '<script>
                    alert("Error: Sesión de usuario inválida o expirada. Por favor, inicie sesión de nuevo.");
                    window.location = "historia_clinica.php";
                </script>';
                return;
            }

            $datos = array(
                "id_paciente" => $_POST["id_paciente"],
                "id_usuario_cuidador" => $id_cuidador,
                "estado_salud" => $_POST["estado_salud"],
                "condiciones" => $_POST["condiciones"],
                "antecedentes_medicos" => $_POST["antecedentes_medicos"],
                "alergias" => $_POST["alergias"],
                "dietas_especiales" => $_POST["dietas_especiales"],
                "observaciones" => $_POST["observaciones"],
                "medicamentos_ids" => $medicamentos_ids,
                "enfermedades_ids" => $enfermedades_ids,
            );

            $respuesta = ModeloHistoriaClinica::mdlCrearHistoriaClinica($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    localStorage.removeItem("historiaClinicaForm");
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

    /*=============================================
    ELIMINAR HISTORIA CLINICA (BORRADO LÓGICO)
    =============================================*/
    static public function ctrEliminarHistoriaClinica()
    {
        if (isset($_GET["idHistoriaClinica"])) {
            $idHistoria = $_GET["idHistoriaClinica"];
            $respuesta = ModeloHistoriaClinica::mdlDesactivarHistoriaClinica($idHistoria);

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