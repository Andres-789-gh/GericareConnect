<?php
// Desactivar informes de errores para no interferir con la descarga del archivo
error_reporting(0);

// Configuración de cabeceras para indicar que se descargará un archivo Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8"); // Usar utf-8 para mejor compatibilidad con Excel
header("Content-Disposition: attachment; filename=enfermedades_" . date('Ymd_His') . ".xls"); // Nombre de archivo dinámico con fecha y hora
header("Pragma: no-cache");
header("Expires: 0");

// Iniciar el buffer de salida para capturar todo el contenido antes de enviarlo
ob_start();

// Incluir el controlador de enfermedades.
// Como este archivo de exportación está ahora en la misma carpeta que el controlador,
// la ruta relativa es simple. El controlador ya incluye el modelo.
require_once __DIR__ . "/enfermedad.controlador.php";

// Obtener los datos de las enfermedades usando tu controlador existente
$enfermedades = ControladorEnfermedades::ctrMostrarEnfermedades(null, null);

// Meta charset para la tabla HTML, importante para caracteres especiales en Excel
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo "<table>
        <thead>
            <tr>
                <th style='background-color:#E0E0E0; font-weight:bold;'>ID</th>
                <th style='background-color:#E0E0E0; font-weight:bold;'>NOMBRE ENFERMEDAD</th>
                <th style='background-color:#E0E0E0; font-weight:bold;'>DESCRIPCIÓN</th>
                <th style='background-color:#E0E0E0; font-weight:bold;'>ESTADO</th>
            </tr>
        </thead>
        <tbody>";

// Iterar sobre los datos y mostrarlos en filas de tabla
if (is_array($enfermedades) && count($enfermedades) > 0) {
    foreach ($enfermedades as $fila) {
        echo "<tr>";
        // utf8_decode se usa para asegurar que los caracteres especiales se muestren bien en Excel
        echo "<td>" . utf8_decode($fila["id_enfermedad"]) . "</td>";
        echo "<td>" . utf8_decode($fila["nombre_enfermedad"]) . "</td>";
        echo "<td>" . utf8_decode($fila["descripcion_enfermedad"]) . "</td>";
        echo "<td>" . utf8_decode($fila["estado"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay datos de enfermedades para exportar.</td></tr>";
}

echo "</tbody>
    </table>";

// Limpiar el buffer de salida y enviarlo al navegador como parte del archivo Excel
ob_end_flush();
exit; // Finaliza la ejecución del script para asegurar que solo se envíe el archivo
?>