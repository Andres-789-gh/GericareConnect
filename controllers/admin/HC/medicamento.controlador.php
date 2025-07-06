<?php

require_once __DIR__ . "/../../models/clases/medicamento.modelo.php";

class ControladorMedicamentosAdmin {

    /**
     * Controlador para crear un nuevo medicamento.
     */
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
                    window.location = "gestion_medicamentos.php?status=success";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear el medicamento.");
                    window.location = "gestion_medicamentos.php";
                </script>';
            }
        }
    }

    /**
     * Controlador para mostrar los medicamentos.
     */
    static public function ctrMostrarMedicamentos($item, $valor) {
        $tabla = "tb_medicamento";
        return ModeloMedicamentos::mdlMostrarMedicamentos($tabla, $item, $valor);
    }

    /**
     * Controlador para editar un medicamento existente.
     */
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
                    window.location = "gestion_medicamentos.php?status=updated";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar el medicamento.");
                    window.location = "gestion_medicamentos.php";
                </script>';
            }
        }
    }

    /**
     * Controlador para eliminar un medicamento (borrado lógico).
     */
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
                    alert("Medicamento desactivado correctamente.");
                    window.location = "gestion_medicamentos.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al desactivar el medicamento.");
                    window.location = "gestion_medicamentos.php";
                </script>';
            }
        }
    }
}