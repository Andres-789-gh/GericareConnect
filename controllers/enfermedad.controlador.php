<?php

require_once "../models/enfermedad.modelo.php";

class ControladorEnfermedades {

    /*=============================================
    Controlador para Crear una Enfermedad (MODIFICADO: 'estado' siempre a 'Activo')
    =============================================*/
    static public function ctrCrearEnfermedad() {
        if (isset($_POST["nombre_enfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => "Activo" // Siempre se crea como Activo
            );

            $respuesta = ModeloEnfermedades::mdlCrearEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Editar una Enfermedad (MODIFICADO: 'estado' ya no se toma del form)
    =============================================*/
    static public function ctrEditarEnfermedad() {
        if (isset($_POST["id_enfermedad_editar"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_POST["id_enfermedad_editar"],
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"]
                // "estado" ya no se maneja directamente desde el formulario de edición
            );

            $respuesta = ModeloEnfermedades::mdlEditarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Mostrar Enfermedades (SIN CAMBIOS)
    =============================================*/
    static public function ctrMostrarEnfermedades($item, $valor) {
        $tabla = "tb_enfermedad";
        $respuesta = ModeloEnfermedades::mdlMostrarEnfermedades($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    Controlador para Cambiar el Estado de una Enfermedad (Se mantiene, es usado por ctrEliminarEnfermedad)
    =============================================*/
    public function ctrCambiarEstadoEnfermedad() {
        if (isset($_GET["idCambiarEstadoEnfermedad"]) && isset($_GET["nuevoEstadoEnfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_GET["idCambiarEstadoEnfermedad"],
                "estado" => $_GET["nuevoEstadoEnfermedad"]
            );

            $respuesta = ModeloEnfermedades::mdlActualizarEstadoEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al cambiar el estado de la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Eliminar una Enfermedad (MODIFICADO: Ahora cambia el estado a Inactivo)
    =============================================*/
    public function ctrEliminarEnfermedad() {
        if (isset($_GET["idEliminarEnfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_GET["idEliminarEnfermedad"],
                "estado" => "Inactivo" // Borrado lógico
            );

            $respuesta = ModeloEnfermedades::mdlActualizarEstadoEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Enfermedad eliminada correctamente.");
                    window.location = "enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al intentar eliminar la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }
}
?>