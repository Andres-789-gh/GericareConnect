<?php

require_once "../models/medicamento.modelo.php"; // Asegúrate de que esta ruta sea correcta

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
                "estado" => $_POST["estado"] // <-- Asegúrate de que este campo esté aquí
            );

            $respuesta = ModeloMedicamentos::mdlCrearMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "index.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear el medicamento.");
                    window.location = "index.php";
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
                "descripcion_medicamento" => $_POST["descripcion_medicamento"],
                "estado" => $_POST["estado"] // <-- Asegúrate de que este campo esté aquí
            );

            $respuesta = ModeloMedicamentos::mdlEditarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "index.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar el medicamento.");
                    window.location = "index.php";
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
    Controlador para Cambiar el Estado del Medicamento (por URL, botones de la tabla)
    =============================================*/
    public function ctrCambiarEstadoMedicamento() {
        if (isset($_GET["idCambiarEstado"]) && isset($_GET["nuevoEstado"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_GET["idCambiarEstado"],
                "estado" => $_GET["nuevoEstado"] // Este valor ya viene del botón y debería ser correcto
            );

            $respuesta = ModeloMedicamentos::mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "index.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al cambiar el estado del medicamento.");
                    window.location = "index.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Eliminar un Medicamento
    =============================================*/
    public function ctrEliminarMedicamento() {
        if (isset($_GET["idEliminar"])) {
            $tabla = "tb_medicamento";
            $datos = $_GET["idEliminar"];

            $respuesta = ModeloMedicamentos::mdlEliminarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "index.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al eliminar el medicamento.");
                    window.location = "index.php";
                </script>';
            }
        }
    }
}
?>