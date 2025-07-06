<?php

require_once __DIR__ . "/../../../models/clases/medicamento.modelo.php";

class ControladorMedicamentos
{

    public function ctrCrearMedicamento()
    {
        if (isset($_POST["nombre_medicamento"])) {
            $tabla = "tb_medicamento";
            $datos = array(
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"],
                "estado" => "Activo"
            );

            // Se crea una instancia del modelo
            $modelo = new ModeloMedicamentos();
            $respuesta = $modelo->mdlCrearMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                // Redirección corregida a la vista de admin
                echo '<script>
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al crear el medicamento.");
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            }
        }
    }

    public function ctrEditarMedicamento()
    {
        if (isset($_POST["id_medicamento_editar"])) {
            $tabla = "tb_medicamento";
            $datos = array(
                "id_medicamento" => $_POST["id_medicamento_editar"],
                "nombre_medicamento" => $_POST["nombre_medicamento"],
                "descripcion_medicamento" => $_POST["descripcion_medicamento"]
            );
            
            // Se crea una instancia del modelo
            $modelo = new ModeloMedicamentos();
            $respuesta = $modelo->mdlEditarMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al editar el medicamento.");
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            }
        }
    }

    public function ctrMostrarMedicamentos($item, $valor)
    {
        $tabla = "tb_medicamento";
        // Se crea una instancia del modelo
        $modelo = new ModeloMedicamentos();
        return $modelo->mdlMostrarMedicamentos($tabla, $item, $valor);
    }

    public function ctrEliminarMedicamento()
    {
        if (isset($_GET["idEliminar"])) {
            $tabla = "tb_medicamento";
            $datos = array(
                "id_medicamento" => $_GET["idEliminar"],
                "estado" => "Inactivo"
            );

            // Se crea una instancia del modelo
            $modelo = new ModeloMedicamentos();
            $respuesta = $modelo->mdlActualizarEstadoMedicamento($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("Medicamento eliminado correctamente.");
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            } else {
                echo '<script>
                    alert("Error al eliminar el medicamento.");
                    window.location = "../../../views/admin/html_admin/medicamento.php";
                </script>';
            }
        }
    }
}