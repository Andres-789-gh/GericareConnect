<?php
// Iniciar sesión para verificar los permisos del usuario
session_start();

// Establecer la cabecera para que la respuesta sea siempre en formato JSON
header('Content-Type: application/json');

// Incluir el archivo que contiene las clases
require_once(__DIR__ . '/../../models/clases/usuario.php');

// Verificación de Seguridad
// Si no hay un rol en la sesión o el rol no es 'Administrador' se deniega el acceso
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    // Se establece un código de error de "No Autorizado"
    http_response_code(403); 
    // Y se devuelve un mensaje de error en formato JSON y se detiene el script
    echo json_encode(['error' => 'Acceso no autorizado. Se requieren permisos de administrador.']);
    exit();
}

// Recolección de Datos de la URL
// Se obtienen los parámetros enviados por el JavaScript a través de la URL con el metodo GET
$filtro_rol = $_GET['filtro'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
// Se obtiene el ID del administrador que realiza la consulta para poder excluirlo de los resultados
$id_admin_actual = $_SESSION['id_usuario'] ?? 0;

try {
    // Se crea una nueva instancia de la clase administrador
    $admin = new administrador();
    
    // Se llama al método consultaGlobal del objeto pasándole los filtros recibidos
    $resultados = $admin->consultaGlobal($filtro_rol, $busqueda, $id_admin_actual);

    // Si la consulta es exitosa se devuelven los resultados codificados en JSON
    echo json_encode($resultados);

} catch (Exception $e) {
    // Si ocurre cualquier error durante la ejecución (como un fallo en la BD) se captura aquí
    // Se establece un código de error de "Error Interno del Servidor"
    http_response_code(500); 
    // Se devuelve el mensaje de error en formato JSON para que el JavaScript lo pueda mostrar
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
