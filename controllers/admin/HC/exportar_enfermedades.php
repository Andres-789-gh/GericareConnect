<?php
// Desactiva la notificación de errores para no corromper el archivo Excel
error_reporting(0);

// Cabeceras para forzar la descarga del archivo como un .xls
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_enfermedades_" . date('Y-m-d_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Inicia el buffer de salida para capturar el HTML
ob_start();

// Incluye el controlador de administrador que ya tiene acceso al modelo
require_once __DIR__ . "/enfermedad.controlador.php";

// Obtén los datos de las enfermedades
$enfermedades = ControladorEnfermedadesAdmin::ctrMostrarEnfermedades(null, null);

// Genera la tabla HTML que se convertirá en el Excel
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo "<table border='1'>
        <thead>
            <tr style='background-color:#E0E0E0; font-weight:bold;'>
                <th>ID</th>
                <th>NOMBRE ENFERMEDAD</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>";

if (is_array($enfermedades) && count($enfermedades) > 0) {
    foreach ($enfermedades as $fila) {
        echo "<tr>";
        // La función utf8_decode asegura la compatibilidad de caracteres especiales
        echo "<td>" . htmlspecialchars($fila["id_enfermedad"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["nombre_enfermedad"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["descripcion_enfermedad"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["estado"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay datos de enfermedades para exportar.</td></tr>";
}

echo "</tbody></table>";

// Envía el contenido del buffer al navegador y finaliza el script
ob_end_flush();
exit;
?>