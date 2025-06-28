<?php

// LA RUTA CORRECTA DESDE controllers/cuidador/ HASTA models/clases/ ES:
require_once "../../models/clases/medicamento.modelo.php";

class ControladorMedicamentos {

    /*=============================================
    Controlador para Crear un Medicamento
    =============================================*/
    static public function ctrCrearMedicamento() {
        if (isset($_POST["nombre_medicamento"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"],
                "estado" => "Activo"
            );

            $respuesta = ModeloMedicamentos::mdlCrearMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    // Redirección desde controlador a vista (sube 2 niveles, entra a views/cuidador/html_cuidador)
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear el medicamento.");
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Editar un Medicamento
    =============================================*/
    static public function ctrEditarMedicamento() {
        if (isset($_POST["id_medicamento_editar"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_POST["id_medicamento_editar"],
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"]
            );

            $respuesta = ModeloMedicamentos::mdlEditarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar el medicamento.");
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Mostrar Medicamentos
    =============================================*/
    static public function ctrMostrarMedicamentos($item, $valor) {
        $tabla = "tb_medicamento";
        $respuesta = ModeloMedicamentos::mdlMostrarMedicamentos($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    Controlador para Cambiar el Estado del Medicamento (Usado para borrado lógico)
    =============================================*/
    public function ctrCambiarEstadoMedicamento() {
        if (isset($_GET["idCambiarEstado"]) && isset($_GET["nuevoEstado"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_GET["idCambiarEstado"],
                "estado" => $_GET["nuevoEstado"]
            );

            $respuesta = ModeloMedicamentos::mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al cambiar el estado del medicamento.");
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Eliminar un Medicamento (Borrado Lógico)
    =============================================*/
    public function ctrEliminarMedicamento() {
        if (isset($_GET["idEliminar"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_GET["idEliminar"],
                "estado" => "Inactivo"
            );

            $respuesta = ModeloMedicamentos::mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Medicamento eliminado lógicamente (estado cambiado a Inactivo).");
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al intentar eliminar lógicamente el medicamento.");
                    window.location = "../views/cuidador/html_cuidador/medicamento.php";
                </script>';
            }
        }
    }
}
?>