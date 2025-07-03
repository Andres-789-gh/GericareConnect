<?php
session_start();
require_once(__DIR__ . '/../models/clases/entrada_salida.php');

$vista_url = '/GericareConnect/views/entradas_salidas/gestion_entradas_salidas.php';

// Solo cuidadores y administradores pueden gestionar esto
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['nombre_rol'], ['Cuidador', 'Administrador'])) {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: /GericareConnect/views/index-login/htmls/index.html");
    exit();
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$entradaSalida = new entradaSalida();

try {
    switch ($accion) {
        case 'registrar':
            $datos = [
                'id_usuario_cuidador'      => $_SESSION['id_usuario'],
                'id_paciente'              => $_POST['id_paciente'],
                'tipo_movimiento'          => $_POST['tipo_movimiento'],
                'motivo'                   => $_POST['motivo'],
                'observaciones'            => $_POST['observaciones'] ?? '',
                'id_usuario_administrador' => ($_SESSION['nombre_rol'] === 'Administrador') ? $_SESSION['id_usuario'] : null
            ];
            $entradaSalida->registrar($datos);
            $_SESSION['mensaje'] = "Registro de " . htmlspecialchars(strtolower($datos['tipo_movimiento'])) . " guardado correctamente.";
            break;

        case 'actualizar_obs':
            $id_registro = $_POST['id_registro'];
            $observaciones = $_POST['observaciones'];
            $entradaSalida->actualizarObservaciones($id_registro, $observaciones);
            $_SESSION['mensaje'] = "Observaciones del registro #$id_registro actualizadas.";
            break;
            
        default:
            $_SESSION['error'] = "Acción no reconocida.";
            break;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error en la operación: " . $e->getMessage();
}

header("Location: " . $vista_url);
exit();
?>