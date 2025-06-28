<?php

// LA RUTA CORRECTA DESDE controllers/cuidador/ HASTA models/clases/ ES:
require_once "../../models/clases/enfermedad.modelo.php";

class ControladorEnfermedades {

    /*=============================================
    Controlador para Crear una Enfermedad
    =============================================*/
    static public function ctrCrearEnfermedad() {
        if (isset($_POST["nombre_enfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => "Activo"
            );

            $respuesta = ModeloEnfermedades::mdlCrearEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    // Redirección desde controlador a vista (sube 2 niveles, entra a views/cuidador/html_cuidador)
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear la enfermedad.");
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Mostrar Enfermedades
    =============================================*/
    static public function ctrMostrarEnfermedades($item, $valor) {
        $tabla = "tb_enfermedad";
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
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"]
            );

            $respuesta = ModeloEnfermedades::mdlEditarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar la enfermedad.");
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            }
        }
    }

    /*================================================
    Controlador para Cambiar el Estado de una Enfermedad (Usado para borrado lógico)
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
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al cambiar el estado de la enfermedad.");
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            }
        }
    }

    /*=============================================
    Controlador para Eliminar una Enfermedad (Borrado Lógico)
    =============================================*/
    public function ctrEliminarEnfermedad() {
        if (isset($_GET["idEliminarEnfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_GET["idEliminarEnfermedad"],
                "estado" => "Inactivo"
            );

            $respuesta = ModeloEnfermedades::mdlActualizarEstadoEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Enfermedad eliminada lógicamente (estado cambiado a Inactivo).");
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al intentar eliminar lógicamente la enfermedad.");
                    window.location = "../views/cuidador/html_cuidador/enfermedad.php";
                </script>';
            }
        }
    }
}