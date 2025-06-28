<?php
session_start();
require_once(__DIR__ . '/../models/clases/paciente.php');
require_once(__DIR__ . '/../models/data_base/database.php');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Acceso no permitido por este método.';
    header('Location: /ruta_a_tu_dashboard.php'); 
    exit();
}


if (!isset($_POST['accion'])) {
    $_SESSION['error'] = 'Acción no especificada.';
    header('Location: ../../views/pacientes/listado.php'); 
    exit();
}


$database = new Database();
$conn = $database->conectar(); 
$paciente_model = new Paciente($conn);

$accion = $_POST['accion'];


switch ($accion) {

    case 'registrar':
        
        $datos = [
            'documento_identificacion' => $_POST['documento_identificacion'] ?? null,
            'nombre'                   => $_POST['nombre'] ?? null,
            'apellido'                 => $_POST['apellido'] ?? null,
            'fecha_nacimiento'         => $_POST['fecha_nacimiento'] ?? null,
            'genero'                   => $_POST['genero'] ?? null,
            'contacto_emergencia'      => $_POST['contacto_emergencia'] ?? null,
            'estado_civil'             => $_POST['estado_civil'] ?? null,
            'tipo_sangre'              => $_POST['tipo_sangre'] ?? null,
            'seguro_medico'            => empty($_POST['seguro_medico']) ? null : $_POST['seguro_medico'],
            'numero_seguro'            => empty($_POST['numero_seguro']) ? null : $_POST['numero_seguro'],
            'id_usuario_familiar'      => empty($_POST['id_usuario_familiar']) ? null : $_POST['id_usuario_familiar'],
        ];

        $resultado = $paciente_model->registrar($datos);

        if ($resultado instanceof Exception) {

            $_SESSION['error'] = 'Error al registrar el paciente: ' . $resultado->getMessage();
            header('Location: ../../views/pacientes/formulario_registro.php'); 
        } else {

            $_SESSION['mensaje'] = 'Paciente registrado con éxito. ID: ' . $resultado['id_paciente_creado'];
            header('Location: ../../views/pacientes/listado.php'); 
        }
        break;


    case 'actualizar':
        $datos = [
            'id_paciente'              => $_POST['id_paciente'] ?? null,
            'documento_identificacion' => $_POST['documento_identificacion'] ?? null,
            'nombre'                   => $_POST['nombre'] ?? null,
            'apellido'                 => $_POST['apellido'] ?? null,
            'fecha_nacimiento'         => $_POST['fecha_nacimiento'] ?? null,
            'genero'                   => $_POST['genero'] ?? null,
            'contacto_emergencia'      => $_POST['contacto_emergencia'] ?? null,
            'estado_civil'             => $_POST['estado_civil'] ?? null,
            'tipo_sangre'              => $_POST['tipo_sangre'] ?? null,
            'seguro_medico'            => empty($_POST['seguro_medico']) ? null : $_POST['seguro_medico'],
            'numero_seguro'            => empty($_POST['numero_seguro']) ? null : $_POST['numero_seguro'],
            'id_usuario_familiar'      => empty($_POST['id_usuario_familiar']) ? null : $_POST['id_usuario_familiar'],
            'estado'                   => $_POST['estado'] ?? 'Activo',
        ];


        if (empty($datos['id_paciente'])) {
            $_SESSION['error'] = 'ID del paciente no especificado para la actualización.';
            header('Location: ../../views/pacientes/listado.php');
            break;
        }

        $resultado = $paciente_model->actualizar($datos);

        if ($resultado instanceof Exception) {
            $_SESSION['error'] = 'Error al actualizar el paciente: ' . $resultado->getMessage();
            header('Location: ../../views/pacientes/formulario_edicion.php?id=' . $datos['id_paciente']);
        } else {
            $_SESSION['mensaje'] = 'Paciente actualizado con éxito.';
            header('Location: ../../views/pacientes/listado.php');
        }
        break;
        

    case 'desactivar':
        $id_paciente = $_POST['id_paciente'] ?? null;

        if (empty($id_paciente)) {
            $_SESSION['error'] = 'No se especificó el ID del paciente a desactivar.';
        } else {
            $resultado = $paciente_model->desactivar($id_paciente);
            if ($resultado instanceof Exception) {
                 $_SESSION['error'] = 'Error al desactivar el paciente: ' . $resultado->getMessage();
            } else {
                $_SESSION['mensaje'] = 'Paciente desactivado correctamente.';
            }
        }
        header('Location: ../../views/pacientes/listado.php');
        break;

    default:

        $_SESSION['error'] = 'Acción desconocida.';
        header('Location: ../../views/pacientes/listado.php');
        break;
}


exit();
?>
