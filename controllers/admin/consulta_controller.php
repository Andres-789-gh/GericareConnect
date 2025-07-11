<?php
// Iniciar sesión para poder verificar el rol y los permisos del usuario que hace la consulta.
session_start();

// Establecer la cabecera para que la respuesta sea siempre en formato JSON.
// Le dice al navegador que este archivo no va a devolver una página web (HTML),
// sino datos puros que el JavaScript de la página de búsqueda puede entender y usar.
header('Content-Type: application/json');

// Incluir el archivo que contiene la clase 'usuario', que tiene el método de búsqueda.
require_once(__DIR__ . '/../../models/clases/usuario.php');

// CAPA DE SEGURIDAD 
// Se verifica que quien hace la petición sea un 'Administrador'. Si no lo es, se le deniega el acceso.
if (!isset($_SESSION['nombre_rol']) || $_SESSION['nombre_rol'] !== 'Administrador') {
    // Se establece un código de error HTTP 403 (Acceso Prohibido).
    http_response_code(403); 
    // Se devuelve un mensaje de error en JSON y se detiene el script.
    echo json_encode(['error' => 'Acceso no autorizado. Se requieren permisos de administrador.']);
    exit();
}

// RECOLECCIÓN DE DATOS DE LA URL (MÉTODO GET)
// Se obtienen los parámetros que el JavaScript envió a través de la URL.
// El operador '??' (fusión de null) es una forma segura de asignar un valor por defecto si el dato no llega.
$filtro_rol = $_GET['filtro'] ?? ''; // El valor del menú desplegable (ej: 'Paciente', 'Cuidador').
$busqueda = $_GET['busqueda'] ?? '';   // El texto que el usuario escribió en la barra de búsqueda.
$id_admin_actual = $_SESSION['id_usuario'] ?? 0; // El ID del admin para excluirlo de los resultados.

// CONSULTA Y MANEJO DE ERRORES
try {
    // Se crea una nueva instancia de la clase 'administrador' para usar sus métodos.
    $admin = new administrador();
    
    // Se llama al método 'consultaGlobal' del modelo, pasándole los filtros que se recogieron.
    // La variable '$resultados' guardará el array de usuarios/pacientes que devuelva la base de datos.
    $resultados = $admin->consultaGlobal($filtro_rol, $busqueda, $id_admin_actual);

    // Si la consulta fue exitosa, se codifican los resultados a formato JSON.
    // 'echo' envía la respuesta de vuelta al JavaScript que hizo la petición.
    echo json_encode($resultados);

} catch (Exception $e) {
    // Si ocurre un error en el bloque 'try' (ej: fallo en la base de datos), el código salta aquí.
    // Se establece un código de error 500 (Error Interno del Servidor).
    http_response_code(500); 
    // Se devuelve el mensaje de error en formato JSON para que el JavaScript lo pueda mostrar al usuario.
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>