<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];

// --- Constantes de Roles (Asegúrate que coincidan con tu BD) ---
define('ADMIN_ROLE_ID', 3);
define('FAMILIAR_ROLE_ID', 1);
define('PACIENTE_ROLE_ID', 4); // Asumiendo que Paciente es el rol 4

// --- Verificación de Sesión y Rol de Admin ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != ADMIN_ROLE_ID) {
    $response['message'] = 'Acceso no autorizado.';
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Recolección de datos del POST ---
    $nombres = isset($_POST['nombres']) ? htmlspecialchars(trim($_POST['nombres'])) : '';
    $apellidos = isset($_POST['apellidos']) ? htmlspecialchars(trim($_POST['apellidos'])) : '';
    $correo = isset($_POST['correo']) ? filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL) : '';
    $tipo_documento = isset($_POST['tipo_documento']) ? htmlspecialchars(trim($_POST['tipo_documento'])) : '';
    $documento = isset($_POST['documento']) ? htmlspecialchars(trim($_POST['documento'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $solicitud_origen_id = isset($_POST['solicitud_origen_id']) && !empty($_POST['solicitud_origen_id']) ? filter_var($_POST['solicitud_origen_id'], FILTER_VALIDATE_INT) : null;
    $familiar_solicitante_id = isset($_POST['familiar_solicitante_id']) && !empty($_POST['familiar_solicitante_id']) ? filter_var($_POST['familiar_solicitante_id'], FILTER_VALIDATE_INT) : null;

    // --- Validaciones ---
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

    // --- Lógica Principal ---
    if (empty($errores)) {
        $conn->begin_transaction();
        try {
            // 1. Verificar si el DOCUMENTO ya existe
            $stmt_check_doc = $conn->prepare("SELECT id FROM usuarios WHERE documento = ?");
            if($stmt_check_doc === false) throw new Exception("Error preparando verificación doc: ".$conn->error);
            $stmt_check_doc->bind_param("s", $documento);
            if(!$stmt_check_doc->execute()) throw new Exception("Error ejecutando verificación doc: ".$stmt_check_doc->error);
            $result_check_doc = $stmt_check_doc->get_result();
            if ($result_check_doc->num_rows > 0) {
                $stmt_check_doc->close();
                throw new Exception("El número de documento ya está registrado.");
            }
            $stmt_check_doc->close();

            // 2. Verificar si el CORREO ya existe y con qué rol
            $stmt_check_correo = $conn->prepare("SELECT id, rol_id FROM usuarios WHERE correo = ?");
             if($stmt_check_correo === false) throw new Exception("Error preparando verificación correo: ".$conn->error);
            $stmt_check_correo->bind_param("s", $correo);
             if(!$stmt_check_correo->execute()) throw new Exception("Error ejecutando verificación correo: ".$stmt_check_correo->error);
            $result_check_correo = $stmt_check_correo->get_result();

            if ($result_check_correo->num_rows > 0) {
                $usuario_existente = $result_check_correo->fetch_assoc();
                // ** LÓGICA MODIFICADA: Permitir si el correo existe pero es de un Familiar **
                // Si el correo existe Y NO es de un familiar, entonces es un error (no puede ser admin, cuidador, u otro paciente con mismo correo)
                if ($usuario_existente['rol_id'] != FAMILIAR_ROLE_ID) {
                     $stmt_check_correo->close();
                     // ** ADVERTENCIA: Permitir que un correo de Familiar se use para un Paciente puede causar problemas si el sistema usa el correo para login único o recuperación de contraseña para todos los roles. **
                     // Si decides no permitirlo, descomenta la siguiente línea:
                      throw new Exception("Este correo electrónico ya está registrado con otro rol.");
                }
                // Si llega aquí, el correo existe pero es de un Familiar, se permite continuar.
            }
            $stmt_check_correo->close();


            // 3. Hashear contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insertar el nuevo usuario PACIENTE
             // Asegúrate que PACIENTE_ROLE_ID (ej. 4) esté definido correctamente arriba
            $sql_insert = "INSERT INTO usuarios (nombres, apellidos, correo, tipo_documento, documento, rol_id, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if($stmt_insert === false) throw new Exception("Error preparando inserción paciente: ".$conn->error);
            $stmt_insert->bind_param("sssssis", $nombres, $apellidos, $correo, $tipo_documento, $documento, PACIENTE_ROLE_ID, $hashed_password);

            if (!$stmt_insert->execute()) {
                // Capturar error específico de MySQL (ej. duplicate key si algo falló en verificación)
                throw new Exception("Error al agregar el paciente: (" . $stmt_insert->errno . ") " . $stmt_insert->error);
            }
            $nuevo_paciente_id = $conn->insert_id; // Obtener el ID del paciente recién creado
            $stmt_insert->close();

            // 5. Crear la relación Familiar-Paciente (si aplica)
            if ($familiar_solicitante_id !== null && $nuevo_paciente_id > 0) {
                 // Verificar que el familiar_id realmente es un familiar (redundante si se valida en origen, pero seguro)
                $stmt_check_familiar_rol = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND rol_id = ?");
                 if($stmt_check_familiar_rol === false) throw new Exception("Error prep. verif. rol familiar: ".$conn->error);
                 $stmt_check_familiar_rol->bind_param("ii", $familiar_solicitante_id, FAMILIAR_ROLE_ID);
                 if(!$stmt_check_familiar_rol->execute()) throw new Exception("Error ejec. verif. rol familiar: ".$stmt_check_familiar_rol->error);
                 $result_check_familiar_rol = $stmt_check_familiar_rol->get_result();

                if ($result_check_familiar_rol->num_rows == 1) {
                     // Insertar la relación
                    $stmt_relacion = $conn->prepare("INSERT INTO familiares_pacientes (familiar_id, paciente_id) VALUES (?, ?)");
                     if($stmt_relacion === false) throw new Exception("Error preparando relación: ".$conn->error);
                    $stmt_relacion->bind_param("ii", $familiar_solicitante_id, $nuevo_paciente_id);
                    if(!$stmt_relacion->execute()) throw new Exception("Error al crear la relación familiar-paciente: ".$stmt_relacion->error);
                    $stmt_relacion->close();
                } else {
                     // Si el ID del familiar no corresponde a un familiar, registrar pero no detener (podría ser opcional)
                     error_log("Intento de relacionar paciente $nuevo_paciente_id con usuario $familiar_solicitante_id que NO es Familiar.");
                }
                 $stmt_check_familiar_rol->close();
            }

            // 6. Actualizar estado de la Solicitud de origen (si aplica)
            if ($solicitud_origen_id !== null && $nuevo_paciente_id > 0) {
                 // Marcarla como Aprobada y asociar el ID del paciente creado
                 // Se usa 'Aprobada' en lugar de 'Procesada' o 'Completada' porque el admin acaba de aprobar el ingreso al CREAR al paciente.
                $stmt_update_sol = $conn->prepare("UPDATE solicitudes SET estado = 'Aprobada', paciente_id_relacionado = ? WHERE id = ? AND tipo_solicitud = 'Ingreso' AND estado = 'Pendiente'"); // Solo actualiza si estaba pendiente
                 if($stmt_update_sol === false) throw new Exception("Error preparando actualización solicitud: ".$conn->error);
                $stmt_update_sol->bind_param("ii", $nuevo_paciente_id, $solicitud_origen_id);
                 if(!$stmt_update_sol->execute()) {
                     // No lanzar excepción aquí, solo loguear, porque el paciente ya se creó.
                     error_log("Error al actualizar solicitud $solicitud_origen_id tras agregar paciente $nuevo_paciente_id: ".$stmt_update_sol->error);
                 } else {
                      // Log si se actualizó
                      if ($stmt_update_sol->affected_rows > 0) {
                           error_log("Solicitud de ingreso #$solicitud_origen_id actualizada a Aprobada y asociada a paciente #$nuevo_paciente_id.");
                      }
                 }
                $stmt_update_sol->close();
            }

            // Si todo fue bien, confirmar transacción
            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Paciente '$nombres $apellidos' agregado correctamente.";
            if ($familiar_solicitante_id) $response['message'] .= " Asociado al familiar.";
            if ($solicitud_origen_id && isset($stmt_update_sol) && $stmt_update_sol->affected_rows > 0) $response['message'] .= " Solicitud #$solicitud_origen_id actualizada.";


        } catch (Exception $e) {
            $conn->rollback(); // Revertir cambios si algo falló
            $response['message'] = $e->getMessage();
            error_log("Error en agregar_paciente_procesar: ".$e->getMessage());
            // Cerrar statements si están abiertos en caso de error temprano
            if(isset($stmt_check_doc) && $stmt_check_doc) $stmt_check_doc->close();
            if(isset($stmt_check_correo) && $stmt_check_correo) $stmt_check_correo->close();
            if(isset($stmt_insert) && $stmt_insert) $stmt_insert->close();
            if(isset($stmt_check_familiar_rol) && $stmt_check_familiar_rol) $stmt_check_familiar_rol->close();
            if(isset($stmt_relacion) && $stmt_relacion) $stmt_relacion->close();
            if(isset($stmt_update_sol) && $stmt_update_sol) $stmt_update_sol->close();
        }

    } else {
        // Si hubo errores de validación inicial
        $response['message'] = $errores; // Devolver array de errores
    }
} else {
    $response['message'] = "Método de solicitud no válido.";
}

// Cerrar conexión y enviar respuesta JSON
if (isset($conn) && $conn) $conn->close();
echo json_encode($response);
?>