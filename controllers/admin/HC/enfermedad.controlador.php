<?php

require_once __DIR__ . "/../../../models/clases/enfermedad.modelo.php";

class ControladorEnfermedades
{

    public function ctrCrearEnfermedad()
    {
        if (isset($_POST["nombre_enfermedad"])) {
            $tabla = "tb_enfermedad";
            $datos = array(
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"],
                "estado" => "Activo"
            );

            // Se crea una instancia del modelo
            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlCrearEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear la enfermedad.");
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            }
        }
    }

    public function ctrMostrarEnfermedades($item, $valor)
    {
        $tabla = "tb_enfermedad";
        // Se crea una instancia del modelo
        $modelo = new ModeloEnfermedades();
        return $modelo->mdlMostrarEnfermedades($tabla, $item, $valor);
    }

    public function ctrEditarEnfermedad()
    {
        if (isset($_POST["id_enfermedad_editar"])) {
            $tabla = "tb_enfermedad";
            $datos = array(
                "id_enfermedad" => $_POST["id_enfermedad_editar"],
                "nombre_enfermedad" => $_POST["nombre_enfermedad"],
                "descripcion_enfermedad" => $_POST["descripcion_enfermedad"]
            );

            // Se crea una instancia del modelo
            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlEditarEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar la enfermedad.");
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            }
        }
    }

    public function ctrEliminarEnfermedad()
    {
        if (isset($_GET["idEliminarEnfermedad"])) {
            $tabla = "tb_enfermedad";
            $datos = array(
                "id_enfermedad" => $_GET["idEliminarEnfermedad"],
                "estado" => "Inactivo"
            );

            // Se crea una instancia del modelo
            $modelo = new ModeloEnfermedades();
            $respuesta = $modelo->mdlActualizarEstadoEnfermedad($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Enfermedad eliminada correctamente.");
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al eliminar la enfermedad.");
                    window.location = "../../../views/admin/html_admin/enfermedad.php";
                </script>';
            }
        }
    }
}