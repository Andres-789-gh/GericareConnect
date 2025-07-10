<?php
// Desactiva la notificación de errores
error_reporting(0);

// Cabeceras para la descarga del archivo Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_medicamentos_" . date('Y-m-d_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Inicia el buffer de salida
ob_start();

// Incluye el controlador de administrador
require_once __DIR__ . "/medicamento.controlador.php";

// Obtén los datos de los medicamentos
$medicamentos = ControladorMedicamentosAdmin::ctrMostrarMedicamentos(null, null);

// Genera la tabla HTML
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo "<table border='1'>
        <thead>
            <tr style='background-color:#E0E0E0; font-weight:bold;'>
                <th>ID</th>
                <th>NOMBRE MEDICAMENTO</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>";

if (is_array($medicamentos) && count($medicamentos) > 0) {
    foreach ($medicamentos as $fila) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila["id_medicamento"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["nombre_medicamento"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["descripcion_medicamento"]) . "</td>";
        echo "<td>" . htmlspecialchars($fila["estado"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay datos de medicamentos para exportar.</td></tr>";
}

echo "</tbody></table>";

// Envía el contenido y finaliza
ob_end_flush();
exit;
?>