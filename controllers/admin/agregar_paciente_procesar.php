<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];


define('ADMIN_ROLE_ID', 3);
define('FAMILIAR_ROLE_ID', 1);
define('PACIENTE_ROLE_ID', 4);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = isset($_POST['nombres']) ? htmlspecialchars(trim($_POST['nombres'])) : '';
    $apellidos = isset($_POST['apellidos']) ? htmlspecialchars(trim($_POST['apellidos'])) : '';
    $correo = isset($_POST['correo']) ? filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL) : '';
    $tipo_documento = isset($_POST['tipo_documento']) ? htmlspecialchars(trim($_POST['tipo_documento'])) : '';
    $documento = isset($_POST['documento']) ? htmlspecialchars(trim($_POST['documento'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $solicitud_origen_id = isset($_POST['solicitud_origen_id']) && !empty($_POST['solicitud_origen_id']) ? filter_var($_POST['solicitud_origen_id'], FILTER_VALIDATE_INT) : null;
    $familiar_solicitante_id = isset($_POST['familiar_solicitante_id']) && !empty($_POST['familiar_solicitante_id']) ? filter_var($_POST['familiar_solicitante_id'], FILTER_VALIDATE_INT) : null;

    $errores = [];

    if (empty($nombres)) $errores[] = "El nombre es requerido.";
    if (empty($apellidos)) $errores[] = "El apellido es requerido.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido.";
    if (empty($tipo_documento)) $errores[] = "El tipo de documento es requerido.";
    if (empty($documento)) $errores[] = "El documento es requerido.";
    if (empty($password)) {
        $errores[] = "La contraseña es requerida.";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($password !== $confirm_password) $errores[] = "Las contraseñas no coinciden.";
    if ($familiar_solicitante_id !== null && $familiar_solicitante_id <= 0) $errores[] = "El ID del familiar solicitante no es válido.";
    if ($solicitud_origen_id !== null && $solicitud_origen_id <= 0) $errores[] = "El ID de la solicitud de origen no es válido.";


    if (empty($errores)) {
        $conn->begin_transaction();
        try {

            $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? OR documento = ?");
            if($stmt_check === false) throw new Exception("Error preparando verificación: ".$conn->error);
            $stmt_check->bind_param("ss", $correo, $documento);
            if(!$stmt_check->execute()) throw new Exception("Error ejecutando verificación: ".$stmt_check->error);
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $stmt_check->close();
                throw new Exception("El correo electrónico o el documento ya están registrados.");
            }
            $stmt_check->close();


            $hashed_password = password_hash($password, PASSWORD_DEFAULT);


            $stmt_rol_paciente = $conn->prepare("SELECT id FROM roles WHERE nombre = 'Paciente'");
            if($stmt_rol_paciente === false) throw new Exception("Error buscando rol Paciente: ".$conn->error);
            if(!$stmt_rol_paciente->execute()) throw new Exception("Error ejecutando búsqueda rol Paciente: ".$stmt_rol_paciente->error);
            $result_rol = $stmt_rol_paciente->get_result();
            if ($result_rol->num_rows != 1) {
                 $stmt_rol_paciente->close();
                 throw new Exception("Error interno: Rol 'Paciente' no encontrado.");
             }
            $rol_paciente_id = $result_rol->fetch_assoc()['id'];
            $stmt_rol_paciente->close();


            $sql_insert = "INSERT INTO usuarios (nombres, apellidos, correo, tipo_documento, documento, rol_id, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
             if($stmt_insert === false) throw new Exception("Error preparando inserción paciente: ".$conn->error);
            $stmt_insert->bind_param("sssssis", $nombres, $apellidos, $correo, $tipo_documento, $documento, $rol_paciente_id, $hashed_password);

            if (!$stmt_insert->execute()) {
                throw new Exception("Error al agregar el paciente: " . $stmt_insert->error);
            }
            $nuevo_paciente_id = $conn->insert_id;
            $stmt_insert->close();


            if ($familiar_solicitante_id !== null && $nuevo_paciente_id) {

                $stmt_check_familiar = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND rol_id = (SELECT id FROM roles WHERE nombre = 'Familiar')");
                 if($stmt_check_familiar === false) throw new Exception("Error preparando verificación familiar: ".$conn->error);
                $stmt_check_familiar->bind_param("i", $familiar_solicitante_id);
                 if(!$stmt_check_familiar->execute()) throw new Exception("Error ejecutando verificación familiar: ".$stmt_check_familiar->error);
                $result_check_familiar = $stmt_check_familiar->get_result();

                if ($result_check_familiar->num_rows == 1) {

                    $stmt_relacion = $conn->prepare("INSERT INTO familiares_pacientes (familiar_id, paciente_id) VALUES (?, ?)");
                     if($stmt_relacion === false) throw new Exception("Error preparando relación: ".$conn->error);
                    $stmt_relacion->bind_param("ii", $familiar_solicitante_id, $nuevo_paciente_id);
                    if(!$stmt_relacion->execute()) throw new Exception("Error al crear la relación familiar-paciente: ".$stmt_relacion->error);
                    $stmt_relacion->close();
                } else {
                     error_log("Se intentó relacionar paciente $nuevo_paciente_id con usuario $familiar_solicitante_id que no es un Familiar válido.");

                }
                $stmt_check_familiar->close();
            }


            if ($solicitud_origen_id !== null && $nuevo_paciente_id) {
                $stmt_update_sol = $conn->prepare("UPDATE solicitudes SET estado = 'Aprobada', paciente_id_relacionado = ? WHERE id = ? AND tipo_solicitud = 'Ingreso'");
                 if($stmt_update_sol === false) throw new Exception("Error preparando actualización solicitud: ".$conn->error);
                $stmt_update_sol->bind_param("ii", $nuevo_paciente_id, $solicitud_origen_id);
                 if(!$stmt_update_sol->execute()) {
                     error_log("Error al actualizar solicitud $solicitud_origen_id tras agregar paciente $nuevo_paciente_id: ".$stmt_update_sol->error);

                 }
                $stmt_update_sol->close();
            }


            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Paciente '$nombres $apellidos' agregado y asociado correctamente.";
            if ($solicitud_origen_id) $response['message'] .= " La solicitud #$solicitud_origen_id ha sido marcada como Aprobada.";


        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = $e->getMessage();
            error_log("Error en agregar_paciente_procesar: ".$e->getMessage());
            if(isset($stmt_insert) && $stmt_insert) $stmt_insert->close();
            if(isset($stmt_relacion) && $stmt_relacion) $stmt_relacion->close();
            if(isset($stmt_update_sol) && $stmt_update_sol) $stmt_update_sol->close();
        }

    } else {
        $response['message'] = $errores;
    }
} else {
    $response['message'] = "Método de solicitud no válido.";
}

if (isset($conn) && $conn) $conn->close();
echo json_encode($response);
?>