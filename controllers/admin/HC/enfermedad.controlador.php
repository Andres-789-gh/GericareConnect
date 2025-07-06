<?php

require_once __DIR__ . "/../../../models/clases/enfermedad.modelo.php";

class ControladorEnfermedadesAdmin {

    /**
     * Controlador para crear una nueva enfermedad.
     */
    static public function ctrCrearEnfermedad() {
        if (isset($_POST["nombre_enfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => "Activo"
            );

            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlCrearEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "gestion_enfermedades.php?status=success";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear la enfermedad.");
                    window.location = "gestion_enfermedades.php";
                </script>';
            }
        }
    }

    /**
     * Controlador para mostrar las enfermedades.
     */
    static public function ctrMostrarEnfermedades($item, $valor) {
        $tabla = "tb_enfermedad";
        $modelo = new ModeloEnfermedades();
        return $modelo->mdlMostrarEnfermedades($tabla, $item, $valor);
    }

    /**
     * Controlador para editar una enfermedad existente.
     */
    static public function ctrEditarEnfermedad() {
        if (isset($_POST["id_enfermedad_editar"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_POST["id_enfermedad_editar"],
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"]
            );

            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlEditarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "gestion_enfermedades.php?status=updated";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar la enfermedad.");
                    window.location = "gestion_enfermedades.php";
                </script>';
            }
        }
    }

    /**
     * Controlador para eliminar una enfermedad (borrado lógico).
     */
    public function ctrEliminarEnfermedad() {
        if (isset($_GET["idEliminarEnfermedad"])) {
            $tabla = "tb_enfermedad";

            $datos = array(
                "id_enfermedad" => $_GET["idEliminarEnfermedad"],
                "estado" => "Inactivo"
            );

            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlActualizarEstadoEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Enfermedad desactivada correctamente.");
                    window.location = "gestion_enfermedades.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al desactivar la enfermedad.");
                    window.location = "gestion_enfermedades.php";
                </script>';
            }
        }
    }
}