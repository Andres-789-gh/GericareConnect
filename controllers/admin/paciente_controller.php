<?php
session_start();
require_once __DIR__ . '/../../models/data_base/database.php';
require_once __DIR__ . '/../../models/clases/pacientes.php';

$database = new Database();
$conn = $database->conectar();
$paciente = new Paciente($conn);

switch ($_POST['accion']) {
    case 'registrar':
        $resultado = $paciente->registrar($_POST);
        $_SESSION['mensaje'] = "Paciente registrado con ID: " . ($resultado['id_paciente_creado'] ?? 'desconocido');
        break;

    case 'actualizar':
        $paciente->actualizar($_POST);
        $_SESSION['mensaje'] = "Paciente actualizado correctamente.";
        break;

    case 'desactivar':
        $paciente->desactivar($_POST['id_paciente']);
        $_SESSION['mensaje'] = "Paciente desactivado.";
        break;

    default:
        $_SESSION['error'] = "Acción no reconocida.";
        break;
}

header('Location: ../../views/pacientes/listado.php');
exit();
?>
