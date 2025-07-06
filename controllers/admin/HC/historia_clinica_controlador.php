<?php
/*
  Controlador para la gestión de Historias Clínicas.
 */

// Inicia la sesión si no está activa, para poder manejar mensajes de feedback al usuario.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requiere el modelo de historia clínica para interactuar con la base de datos.
require_once(__DIR__ . '/../../../models/clases/historia_clinica_modelo.php');

class ControladorHistoriaClinica {

    // Propiedad para almacenar la instancia del modelo.
    private $modelo;

    /**
     * Constructor de la clase.
     * Al crear un objeto ControladorHistoriaClinica, también se crea una instancia del ModeloHistoriaClinica.
     */
    public function __construct() {
        $this->modelo = new ModeloHistoriaClinica();
    }

    /*
      Procesa la solicitud para crear una nueva historia clínica.
      Valida los datos recibidos por POST y llama al modelo.
     */
    public function registrar() {
        // Verifica que se hayan enviado los datos necesarios desde un formulario.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id_paciente"])) {

            // Se asegura de que el administrador esté logueado.
            if (!isset($_SESSION["id_usuario"])) {
                $_SESSION['error'] = "Error: Sesión de administrador inválida o expirada.";
                header("Location: ../../views/admin/html_admin/historia_clinica.php");
                exit();
            }

            // Recopila los datos del formulario.
            $datos = [
                "id_paciente" => $_POST["id_paciente"],
                "id_usuario_administrador" => $_SESSION["id_usuario"],
                "estado_salud" => trim($_POST["estado_salud"]),
                "condiciones" => trim($_POST["condiciones"]),
                "antecedentes_medicos" => trim($_POST["antecedentes_medicos"]),
                "alergias" => trim($_POST["alergias"]),
                "dietas_especiales" => trim($_POST["dietas_especiales"]),
                "fecha_ultima_consulta" => date('Y-m-d'), // Fecha actual
                "observaciones" => trim($_POST["observaciones"])
            ];

            // Llama al método del modelo para registrar la historia.
            $respuesta = $this->modelo->mdlRegistrarHistoriaClinica($datos);

            // Redirige al usuario con un mensaje de éxito o error.
            if ($respuesta == "ok") {
                $_SESSION['mensaje'] = "¡Historia Clínica creada con éxito!";
            } else {
                $_SESSION['error'] = "Error al crear la historia clínica. Inténtelo de nuevo.";
            }
            header("Location: ../../../views/admin/html_admin/historia_clinica.php");
            exit();
        }
    }
    
    /*
      Procesa la solicitud para actualizar una historia clínica existente.
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id_historia_clinica"])) {
            
            $datos = [
                "id_historia_clinica" => $_POST["id_historia_clinica"],
                "id_usuario_administrador" => $_SESSION["id_usuario"], // El admin que realiza el cambio
                "estado_salud" => trim($_POST["estado_salud"]),
                "condiciones" => trim($_POST["condiciones"]),
                "antecedentes_medicos" => trim($_POST["antecedentes_medicos"]),
                "alergias" => trim($_POST["alergias"]),
                "dietas_especiales" => trim($_POST["dietas_especiales"]),
                "fecha_ultima_consulta" => date('Y-m-d'),
                "observaciones" => trim($_POST["observaciones"]),
                "estado" => "Activo"
            ];
            
            $respuesta = $this->modelo->mdlActualizarHistoriaClinica($datos);

            if ($respuesta == "ok") {
                $_SESSION['mensaje'] = "Historia Clínica actualizada correctamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar la historia clínica.";
            }
            // Redirige de vuelta a la lista principal
            header("Location: ../../views/admin/html_admin/historia_clinica.php");
            exit();
        }
    }

    /*
     Procesa la solicitud para eliminar (desactivar)
     */
    public function eliminar() {
        if (isset($_GET["idHistoriaEliminar"])) {
            $idHistoria = $_GET["idHistoriaEliminar"];
            $respuesta = $this->modelo->mdlEliminarHistoriaClinica($idHistoria);

            if ($respuesta == "ok") {
                $_SESSION['mensaje'] = "Historia clínica eliminada correctamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar la historia clínica.";
            }
            header("Location: ../../views/admin/html_admin/historia_clinica.php");
            exit();
        }
    }
    
    /**
     * Obtiene y devuelve las historias clínicas para ser mostradas en la vista.
     * @param string|null $item El campo por el cual filtrar.
     * @param mixed|null $valor El valor a buscar.
     * @return array Lista de historias clínicas.
     */
    public function mostrar($item = null, $valor = null) {
        return $this->modelo->mdlConsultarHistoriaClinica($item, $valor);
    }
}


/*
Lógica de Enrutamiento
----------------------------------------------------------------------------
Esta sección se encarga de decidir qué método del controlador llamar
basándose en los datos recibidos por POST o GET.
 */

$controlador = new ControladorHistoriaClinica();

// Si se está intentando registrar una nueva historia:
if (isset($_POST['accion']) && $_POST['accion'] == 'registrar') {
    $controlador->registrar();
}

// Si se está intentando actualizar una historia existente:
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    $controlador->actualizar();
}

// Si se está intentando eliminar una historia desde la URL:
if (isset($_GET['idHistoriaEliminar'])) {
    $controlador->eliminar();
}

// La función 'mostrar' se llamaría directamente desde el archivo de la vista (la página PHP que muestra la tabla).

?>