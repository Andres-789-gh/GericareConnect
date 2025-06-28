<?php

require_once "../models/medicamento.modelo.php";

class ControladorMedicamentos {

    /*=============================================
    Controlador para Crear un Medicamento (MODIFICADO: 'estado' siempre a 'Activo')
    =============================================*/
    static public function ctrCrearMedicamento() {
        if (isset($_POST["nombre_medicamento"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"],
                "estado" => "Activo" // Siempre se crea como Activo
            );

            $respuesta = ModeloMedicamentos::mdlCrearMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "medicamentos.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear el medicamento.");
                    window.location = "medicamentos.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Editar un Medicamento (MODIFICADO: 'estado' ya no se toma del form)
    =============================================*/
    static public function ctrEditarMedicamento() {
        if (isset($_POST["id_medicamento_editar"])) {
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_POST["id_medicamento_editar"],
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"]
                // "estado" ya no se maneja directamente desde el formulario de edición
            );

            $respuesta = ModeloMedicamentos::mdlEditarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "medicamentos.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar el medicamento.");
                    window.location = "medicamentos.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Mostrar Medicamentos (SIN CAMBIOS)
    =============================================*/
    static public function ctrMostrarMedicamentos($item, $valor) {
        $tabla = "tb_medicamento";
        $respuesta = ModeloMedicamentos::mdlMostrarMedicamentos($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    Controlador para Cambiar el Estado del Medicamento (MODIFICADO: ahora este maneja el borrado lógico)
    =============================================*/
    // Este método seguirá existiendo pero su llamada desde la vista cambiará para el borrado lógico
    public function ctrCambiarEstadoMedicamento() {
        if (isset($_GET["idCambiarEstado"]) && isset($_GET["nuevoEstado"])) { // Estos GET vendrán de los enlaces de borrado lógico
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_GET["idCambiarEstado"],
                "estado" => $_GET["nuevoEstado"] // Será 'Inactivo' cuando se "elimine"
            );

            $respuesta = ModeloMedicamentos::mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "medicamentos.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al cambiar el estado del medicamento.");
                    window.location = "medicamentos.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Eliminar un Medicamento (MODIFICADO: Ahora llama a cambiar estado)
    =============================================*/
    public function ctrEliminarMedicamento() {
        if (isset($_GET["idEliminar"])) {
            // No se elimina físicamente, se cambia el estado a 'Inactivo'
            $tabla = "tb_medicamento";

            $datos = array(
                "id_medicamento" => $_GET["idEliminar"],
                "estado" => "Inactivo" // Este es el "borrado lógico"
            );

            // Reutilizamos el método para actualizar estado
            $respuesta = ModeloMedicamentos::mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Medicamento eliminado lógicamente (estado cambiado a Inactivo).");
                    window.location = "medicamentos.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al intentar eliminar lógicamente el medicamento.");
                    window.location = "medicamentos.php";
                </script>';
            }
        }
    }
}
?>