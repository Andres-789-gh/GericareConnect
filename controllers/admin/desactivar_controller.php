<?php
// Inicia la sesión para poder leer el rol y el ID del administrador.
session_start();
// Esta cabecera le dice al navegador que la respuesta de este archivo siempre será en formato JSON.
// porque este script no muestra una página HTML, sino que le responde a una llamada de JavaScript.
header('Content-Type: application/json');

// Se cargan las clases:
// 'usuario.php' contiene el método para desactivar usuarios (familiares, cuidadores).
require_once(__DIR__ . '/../../models/clases/usuario.php');
// 'pacientes.php' contiene el método para desactivar pacientes.
require_once(__DIR__ . '/../../models/clases/pacientes.php');

// CAPA DE SEGURIDAD 
// Se asegura de que solo un usuario con el rol de 'Administrador' pueda ejecutar este código.
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    // Si no es un admin, se envía un código de error HTTP 403 (Acceso Prohibido).
    http_response_code(403);
    // Se devuelve un mensaje de error en JSON y se detiene el script.
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

// RECOLECCIÓN Y VALIDACIÓN DE DATOS
// Se recogen los datos que envió el JavaScript desde la página del administrador.
// "?? 0" y "?? '' " son operadores de fusión de null. Sirven para evitar errores si los datos no llegan,
// asignando un valor por defecto.
$id_entidad = $_POST['id'] ?? 0;     // El ID del usuario o paciente a desactivar.
$tipo_entidad = $_POST['tipo'] ?? ''; // El tipo ('Usuario' o 'Paciente') para saber qué hacer.
$id_admin_actual = $_SESSION['id_usuario'] ?? 0; // El ID del admin que está realizando la acción.

// Se valida que los datos recibidos sean válidos.
if ($id_entidad <= 0 || empty($tipo_entidad)) {
    // Si faltan datos, se devuelve un código de error 400 (Solicitud incorrecta).
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
    exit();
}

//  LÓGICA DESACTIVACIÓN 
try {
    // Se crean los objetos de las clases.
    $admin = new administrador();
    $paciente = new Paciente();
    
    // Se usa un "if" y "else if" para decidir qué acción tomar basado en el tipo_entidad.
    if ($tipo_entidad === 'Usuario') {
        // Si se va a desactivar un usuario, se llama al método 'desactivarUsuario'.
        $admin->desactivarUsuario($id_entidad, $id_admin_actual);
        $message = 'Usuario desactivado correctamente.';
    } elseif ($tipo_entidad === 'Paciente') {
        // Si se va a desactivar un paciente, se llama al método 'desactivar'.
        $paciente->desactivar($id_entidad);
        $message = 'Paciente desactivado correctamente.';
    } else {
        // Si llega un tipo no reconocido, se lanza una excepción para que sea capturada por el 'catch'.
        throw new Exception('Tipo de entidad no reconocido.');
    }

    // Si todo sale bien, se envía una respuesta JSON de éxito.
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    // MANEJO DE ERRORES 
    // Si ocurre cualquier error en el bloque 'try', el código salta aquí.
    // Se establece un código de error 500 (Error Interno del Servidor).
    http_response_code(500);
    // Se "limpia" el mensaje de error de la base de datos para mostrar solo la parte relevante y amigable.
    $errorMessage = explode(":", $e->getMessage());
    // Se envía una respuesta JSON de error con el mensaje limpio.
    echo json_encode(['success' => false, 'message' => trim(end($errorMessage))]);
}
?>