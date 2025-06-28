<?php
// Iniciar sesión para cualquier verificación de seguridad futura
session_start();
// Establecer el tipo de contenido a JSON para la respuesta AJAX
header('Content-Type: application/json');

// Incluir la clase Paciente. La ruta es relativa a la ubicación de este archivo.
// Asumiendo que 'controllers' y 'models' están al mismo nivel.
require_once __DIR__ . '/../../models/clases/pacientes.php';

// Respuesta por defecto
$response = ['success' => false, 'message' => 'Error: Solicitud no válida.'];

// --- Verificación de Sesión y Rol (Opcional pero recomendado) ---
/*
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] != 3) { // Asumiendo que 3 es el ID de Admin
    $response['message'] = 'Error: Acceso no autorizado.';
    echo json_encode($response);
    exit();
}
*/

// --- Se procesa solo si el método es POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- IMPORTANTE: Recolección de TODOS los datos del formulario ---
    // Tu formulario HTML debe tener campos para todos estos datos.
    $datos_paciente = [
        'documento_identificacion' => filter_input(INPUT_POST, 'documento', FILTER_SANITIZE_NUMBER_INT),
        'nombre'                   => filter_input(INPUT_POST, 'nombres', FILTER_SANITIZE_STRING),
        'apellido'                 => filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING),
        'fecha_nacimiento'         => $_POST['fecha_nacimiento'] ?? null,
        'genero'                   => $_POST['genero'] ?? null,
        'contacto_emergencia'      => filter_input(INPUT_POST, 'contacto_emergencia', FILTER_SANITIZE_STRING),
        'estado_civil'             => filter_input(INPUT_POST, 'estado_civil', FILTER_SANITIZE_STRING),
        'tipo_sangre'              => filter_input(INPUT_POST, 'tipo_sangre', FILTER_SANITIZE_STRING),
        'seguro_medico'            => filter_input(INPUT_POST, 'seguro_medico', FILTER_SANITIZE_STRING),
        'numero_seguro'            => filter_input(INPUT_POST, 'numero_seguro', FILTER_SANITIZE_STRING),
        'id_usuario_familiar'      => filter_input(INPUT_POST, 'familiar_solicitante_id', FILTER_VALIDATE_INT) ?: null
    ];
    
    // --- Validaciones del Lado del Servidor ---
    $errores = [];
    if (empty($datos_paciente['documento_identificacion'])) $errores[] = "El número de documento es requerido.";
    if (empty($datos_paciente['nombre'])) $errores[] = "El nombre es requerido.";
    if (empty($datos_paciente['apellido'])) $errores[] = "El apellido es requerido.";
    if (empty($datos_paciente['fecha_nacimiento'])) $errores[] = "La fecha de nacimiento es requerida.";
    if (empty($datos_paciente['genero'])) $errores[] = "El género es requerido.";
    // Agrega aquí más validaciones si es necesario...
    
    // --- Lógica Principal para registrar ---
    if (empty($errores)) {
        try {
            // 1. Instanciar la clase Paciente.
            // La clase se encargará de la conexión a la BD por sí misma.
            $paciente = new Paciente();

            // 2. Llamar al método registrar.
            $resultado = $paciente->registrar($datos_paciente);

            // 3. Verificar el resultado del procedimiento almacenado.
            if ($resultado && isset($resultado['id_paciente_creado'])) {
                $response['success'] = true;
                $response['message'] = "Paciente '{$datos_paciente['nombre']} {$datos_paciente['apellido']}' agregado correctamente.";
                $response['paciente_id'] = $resultado['id_paciente_creado'];
            } else {
                // Esto puede ocurrir si el SP no retorna el ID o falla.
                $response['message'] = "Error: No se pudo confirmar el registro en la base de datos.";
            }

        } catch (PDOException $e) {
            // Captura errores específicos de la base de datos (PDO).
            $response['message'] = "Error de base de datos. Detalles: " . $e->getMessage();
            // Es buena práctica registrar el error para depuración.
            error_log("Error en agregar_paciente_procesar.php (PDO): " . $e->getMessage());
        } catch (Exception $e) {
            // Captura cualquier otro error general.
            $response['message'] = "Error inesperado en el servidor. Detalles: " . $e->getMessage();
            error_log("Error en agregar_paciente_procesar.php (General): " . $e->getMessage());
        }
    } else {
        // Si hubo errores de validación, se devuelven.
        $response['message'] = implode("<br>", $errores);
    }
}

// Se envía la respuesta final en formato JSON al frontend.
echo json_encode($response);
?>

