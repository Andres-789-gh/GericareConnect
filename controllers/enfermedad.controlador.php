<?php

require_once "../models/enfermedad.modelo.php"; // Requerimos el modelo de enfermedad

class ControladorEnfermedades {

    /*=============================================
    Controlador para Crear una Enfermedad
    =============================================*/
    static public function ctrCrearEnfermedad() {
        if (isset($_POST["nombre_enfermedad"])) {
            $tabla = "tb_enfermedad"; // Nombre de tu tabla en la base de datos

            $datos = array(
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => $_POST["estado"] // Tomamos el estado del formulario
            );

            $respuesta = ModeloEnfermedades::mdlCrearEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php"; // Redirige a la vista de enfermedades
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
    Controlador para Mostrar Enfermedades
    =============================================*/
    static public function ctrMostrarEnfermedades($item, $valor) {
        $tabla = "tb_enfermedad"; // Nombre de tu tabla
        $respuesta = ModeloEnfermedades::mdlMostrarEnfermedades($tabla, $item, $valor);
        return $respuesta;
    }

    /*================================================
    Controlador para Editar una Enfermedad
    ================================================*/
    static public function ctrEditarEnfermedad() {
        if (isset($_POST["id_enfermedad_editar"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_POST["id_enfermedad_editar"],
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => $_POST["estado"] // Tomamos el estado del formulario
            );

            $respuesta = ModeloEnfermedades::mdlEditarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php"; // Redirige a la vista de enfermedades
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }

    /*================================================
    Controlador para Cambiar el Estado de una Enfermedad
    ================================================*/
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
                    window.location = "enfermedad.php"; // Redirige a la vista de enfermedades
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
    Controlador para Eliminar una Enfermedad
    =============================================*/
    public function ctrEliminarEnfermedad() {
        if (isset($_GET["idEliminarEnfermedad"])) {
            $tabla = "tb_enfermedad";
            $datos = $_GET["idEliminarEnfermedad"];

            $respuesta = ModeloEnfermedades::mdlEliminarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "enfermedad.php"; // Redirige a la vista de enfermedades
                </script>';
            } else {
                echo '<script>
                    alert("Error al eliminar la enfermedad.");
                    window.location = "enfermedad.php";
                </script>';
            }
        }
    }
}

?>