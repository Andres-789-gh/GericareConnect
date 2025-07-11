<?php
// Requerir los modelos necesarios
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

// Iniciar el buffer de salida para evitar errores
ob_start();

// Capturar los filtros de la URL
$id_cuidador_filtro = $_GET['cuidador'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

// --- Lógica para obtener los datos ---
$modelo_actividad = new Actividad();
$modelo_usuario = new usuario();

$actividades = [];
$nombre_cuidador = "N/A";

if (!empty($id_cuidador_filtro)) {
    $actividades = $modelo_actividad->consultarPorCuidador($id_cuidador_filtro, $busqueda, $estado_filtro);
    $cuidador_info = $modelo_usuario->obtenerPorId($id_cuidador_filtro);
    if ($cuidador_info) {
        $nombre_cuidador = $cuidador_info['nombre'] . ' ' . $cuidador_info['apellido'];
    }
}

// Cabeceras para forzar la descarga del archivo como un .xls
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_actividades_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache"); //No guardar archivo en caché.
header("Expires: 0"); //Se considera obsoleto para que no lo guarde ne cache

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
</head>
<body>
    <table border="1">
        <tr>
            <td colspan="7" style="background-color:#EAF1FB; font-size: 18px; font-weight:bold; text-align:center;">Reporte de Actividades</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Cuidador:</td>
            <td colspan="2"><?= $nombre_cuidador ?></td>
            <td style="font-weight:bold;">Fecha de Reporte:</td>
            <td colspan="3"><?= date('d/m/Y') ?></td>
        </tr>
        <tr></tr>
        <thead style="background-color:#F2F2F2; font-weight:bold;">
            <tr>
                <th>Tipo de Actividad</th>
                <th>Paciente Asignado</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Estado</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($actividades) && count($actividades) > 0): ?>
                <?php foreach ($actividades as $fila): ?>
                    <tr>
                        <td><?= $fila["tipo_actividad"] ?></td>
                        <td><?= $fila["nombre_paciente"] ?></td>
                        <td><?= date("d/m/Y", strtotime($fila["fecha_actividad"])) ?></td>
                        <td><?= $fila["hora_inicio"] ?? 'N/A' ?></td>
                        <td><?= $fila["hora_fin"] ?? 'N/A' ?></td>
                        <td><?= $fila["estado_actividad"] ?></td>
                        <td><?= $fila["descripcion_actividad"] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay datos para exportar con los filtros seleccionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Envía el contenido del buffer al navegador y finaliza el script
ob_end_flush();
exit;
?>