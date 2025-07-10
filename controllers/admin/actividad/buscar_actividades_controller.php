<?php
// 1. Establecemos que la respuesta será en formato JSON
header('Content-Type: application/json');

// 2. Incluimos los archivos necesarios
// Asegúrate que la ruta a tu modelo es correcta
require_once __DIR__ . '/../../../models/clases/actividad.php'; 
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);

// 3. Capturamos los filtros de la URL (enviados por fetch)
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

try {
    // 4. Creamos una instancia del modelo
    $modelo_actividad = new Actividad();

    // 5. Obtenemos los datos del método consultar() del modelo
    $actividades = $modelo_actividad->consultar($busqueda, $estado_filtro);

    // 6. Convertimos el resultado a JSON y lo enviamos como respuesta
    echo json_encode($actividades);

} catch (Exception $e) {
    // Manejo básico de errores: devuelve un error 500 y el mensaje
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error en el servidor: ' . $e->getMessage()]);
}

// 7. Terminamos la ejecución para no enviar nada más
exit;
?>