<?php
session_start();
header('Content-Type: application/json');

// Requerir dependencias
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

// Verificar que sea un administrador
verificarAcceso(['Administrador']);

$modelo = new HistoriaClinica();
$response = ['success' => false, 'message' => 'Acción no reconocida.'];

if (isset($_POST['accion'])) {
    try {
        switch ($_POST['accion']) {
            // --- ACCIONES DE ENFERMEDADES ---
            case 'asignar_enfermedad':
                $result = $modelo->asignarEnfermedad($_POST['id_historia_clinica'], $_POST['id_enfermedad']);
                if ($result['id_asignacion'] > 0) {
                    $response = ['success' => true, 'id_asignacion' => $result['id_asignacion']];
                } else {
                    throw new Exception("La enfermedad ya está asignada.");
                }
                break;

            case 'eliminar_enfermedad':
                $modelo->eliminarEnfermedadAsignada($_POST['id_hc_enfermedad']);
                $response = ['success' => true];
                break;

            // --- ACCIONES DE MEDICAMENTOS ---
            case 'asignar_medicamento':
                $datos = [
                    'id_historia_clinica' => $_POST['id_historia_clinica'],
                    'id_medicamento' => $_POST['id_medicamento'],
                    'dosis' => $_POST['dosis'],
                    'frecuencia' => $_POST['frecuencia'],
                    'instrucciones' => $_POST['instrucciones']
                ];
                $result = $modelo->asignarMedicamento($datos);
                $response = ['success' => true, 'id_asignacion' => $result['id_asignacion']];
                break;

            case 'actualizar_medicamento':
                 $datos = [
                    'id_hc_medicamento' => $_POST['id_hc_medicamento'],
                    'dosis' => $_POST['dosis'],
                    'frecuencia' => $_POST['frecuencia'],
                    'instrucciones' => $_POST['instrucciones']
                ];
                $modelo->actualizarMedicamentoAsignado($datos);
                $response = ['success' => true];
                break;

            case 'eliminar_medicamento':
                $modelo->eliminarMedicamentoAsignado($_POST['id_hc_medicamento']);
                $response = ['success' => true];
                break;
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
}

echo json_encode($response);
exit();
?>