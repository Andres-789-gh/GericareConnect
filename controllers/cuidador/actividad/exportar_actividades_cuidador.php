<?php
// Iniciar sesión y verificar que el usuario sea un Cuidador
session_start();
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Cuidador']);

// Requerir los modelos necesarios
require_once __DIR__ . '/../../../models/clases/actividad.php';

// Iniciar el buffer de salida para evitar errores en la generación del archivo
ob_start();

// Capturar los filtros de la URL (si los hay)
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';
$id_cuidador = $_SESSION['id_usuario']; // Obtener el ID del cuidador de la sesión

// --- Lógica para obtener los datos ---
$modelo_actividad = new Actividad();

// Consultar las actividades específicas de este cuidador
$actividades = $modelo_actividad->consultarPorCuidador($id_cuidador, $busqueda, $estado_filtro);
$nombre_cuidador = $_SESSION['nombre_usuario'] ?? 'Cuidador'; // Puedes mejorar esto si guardas el nombre en la sesión

// Cabeceras para forzar la descarga del archivo como un .xls
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_mis_actividades_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mis Actividades</title>
</head>
<body>
    <table border="1">
        <tr>
            <td colspan="7" style="background-color:#EAF1FB; font-size: 18px; font-weight:bold; text-align:center;">
                Reporte de Actividades Asignadas
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Cuidador:</td>
            <td colspan="2"><?= htmlspecialchars($nombre_cuidador) ?></td>
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
                        <td><?= htmlspecialchars($fila["tipo_actividad"]) ?></td>
                        <td><?= htmlspecialchars($fila["nombre_paciente"]) ?></td>
                        <td><?= htmlspecialchars(date("d/m/Y", strtotime($fila["fecha_actividad"]))) ?></td>
                        <td><?= htmlspecialchars($fila["hora_inicio"] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($fila["hora_fin"] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($fila["estado_actividad"]) ?></td>
                        <td><?= htmlspecialchars($fila["descripcion_actividad"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay actividades para exportar con los filtros seleccionados.</td>
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