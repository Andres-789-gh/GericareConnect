<?php
// Desactivar la notificación de errores para no dañar el archivo Excel
error_reporting(0);
ob_start(); // Iniciar el buffer de salida

// Requerir los modelos necesarios
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

// Cabeceras para forzar la descarga del archivo como un .xls
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_actividades_cuidador_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// --- Lógica para obtener los datos ---
$modelo_actividad = new Actividad();
$modelo_usuario = new usuario();

// Capturar los filtros de la URL
$id_cuidador_filtro = $_GET['cuidador'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

$actividades = [];
$nombre_cuidador = "Todos";

if (!empty($id_cuidador_filtro)) {
    $actividades = $modelo_actividad->consultarPorCuidador($id_cuidador_filtro, $busqueda, $estado_filtro);
    $cuidador_info = $modelo_usuario->obtenerPorId($id_cuidador_filtro);
    if ($cuidador_info) {
        $nombre_cuidador = $cuidador_info['nombre'] . ' ' . $cuidador_info['apellido'];
    }
}

// --- Generar la tabla HTML que se convertirá en Excel ---
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo "<h1>Reporte de Actividades</h1>";
echo "<h3>Cuidador: " . utf8_decode($nombre_cuidador) . "</h3>";
echo "<h3>Fecha de Reporte: " . date('d/m/Y') . "</h3>";

echo "<table border='1'>
        <thead>
            <tr style='background-color:#007bff; color:white; font-weight:bold;'>
                <th>ID Actividad</th>
                <th>Tipo de Actividad</th>
                <th>Paciente Asignado</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Descripci&oacute;n</th>
            </tr>
        </thead>
        <tbody>";

if (is_array($actividades) && count($actividades) > 0) {
    foreach ($actividades as $fila) {
        echo "<tr>";
        echo "<td>" . $fila["id_actividad"] . "</td>";
        echo "<td>" . utf8_decode($fila["tipo_actividad"]) . "</td>";
        echo "<td>" . utf8_decode($fila["nombre_paciente"]) . "</td>";
        echo "<td>" . date("d/m/Y", strtotime($fila["fecha_actividad"])) . "</td>";
        echo "<td>" . utf8_decode($fila["estado_actividad"]) . "</td>";
        echo "<td>" . utf8_decode($fila["descripcion_actividad"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay datos para exportar con los filtros seleccionados.</td></tr>";
}

echo "</tbody></table>";

// Envía el contenido del buffer al navegador y finaliza el script
ob_end_flush();
exit;
?>