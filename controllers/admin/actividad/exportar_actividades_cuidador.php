<?php
// Requerir los modelos para acceder a la base de datos.
require_once __DIR__ . '/../../../models/clases/actividad.php';
require_once __DIR__ . '/../../../models/clases/usuario.php';

// Inicia un buffer de salida. Esto captura todo el HTML generado para enviarlo después como un solo bloque.
// Es crucial para que las cabeceras 'header()' funcionen correctamente.
ob_start();

// --- RECOLECCIÓN DE DATOS ---

// Captura los filtros enviados desde la página anterior a través de la URL (GET).
// El operador '??' asigna un valor vacío ('') si el filtro no existe, para evitar errores.
$id_cuidador_filtro = $_GET['cuidador'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

// --- CONSULTA A LA BASE DE DATOS ---

// Instancia los modelos para poder usar sus funciones.
$modelo_actividad = new Actividad();
$modelo_usuario = new usuario();

// Prepara las variables que contendrán los datos para el reporte.
$actividades = [];
$nombre_cuidador = "N/A"; // Valor por defecto.

// Solo si se ha seleccionado un cuidador, se procede a buscar los datos.
if (!empty($id_cuidador_filtro)) {
    // Llama al modelo para obtener las actividades, aplicando los filtros.
    $actividades = $modelo_actividad->consultarPorCuidador($id_cuidador_filtro, $busqueda, $estado_filtro);
    
    // Obtiene los datos del cuidador seleccionado para mostrar su nombre en el reporte.
    $cuidador_info = $modelo_usuario->obtenerPorId($id_cuidador_filtro);
    
    // Si se encontró la información del cuidador, se construye su nombre completo.
    if ($cuidador_info) {
        $nombre_cuidador = $cuidador_info['nombre'] . ' ' . $cuidador_info['apellido'];
    }
}

// --- PREPARACIÓN DEL ARCHIVO EXCEL ---

// Se envían cabeceras HTTP que le ordenan al navegador cómo manejar el archivo.
// Content-Type: Le dice al navegador que el archivo es un Excel y que use codificación UTF-8.
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
// Content-Disposition: Fuerza la descarga del archivo y le asigna un nombre dinámico con la fecha actual.
header("Content-Disposition: attachment; filename=reporte_actividades_" . date('Y-m-d') . ".xls");
// Pragma: no-cache: Evita que el navegador guarde una copia en caché.
header("Pragma: no-cache");
// Expires: 0: Indica que el archivo expira inmediatamente, forzando una nueva descarga siempre.
header("Expires: 0");

?>
<!-- A partir de aquí, se genera el HTML que Excel interpretará como el contenido del archivo. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
</head>
<body>
    <table border="1">
        <!-- Encabezado principal del reporte. -->
        <tr>
            <td colspan="7" style="background-color:#EAF1FB; font-size: 18px; font-weight:bold; text-align:center;">Reporte de Actividades</td>
        </tr>
        <!-- Fila con información del cuidador y la fecha de generación. -->
        <tr>
            <td style="font-weight:bold;">Cuidador:</td>
            <td colspan="2"><?= $nombre_cuidador ?></td>
            <td style="font-weight:bold;">Fecha de Reporte:</td>
            <td colspan="3"><?= date('d/m/Y') ?></td>
        </tr>
        <!-- Fila vacía para dar espacio. -->
        <tr></tr>
        <!-- Cabecera de la tabla de datos. -->
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
            <?php 
            // Se verifica si el array de actividades contiene datos.
            if (is_array($actividades) && count($actividades) > 0): 
            ?>
                <?php 
                // Se recorre el array de actividades para crear una fila por cada una.
                foreach ($actividades as $fila): 
                ?>
                    <tr>
                        <!-- Se imprime cada dato en su celda correspondiente. -->
                        <td><?= $fila["tipo_actividad"] ?></td>
                        <td><?= $fila["nombre_paciente"] ?></td>
                        <td><?= date("d/m/Y", strtotime($fila["fecha_actividad"])) ?></td>
                        <td><?= $fila["hora_inicio"] ?? 'N/A' ?></td>
                        <td><?= $fila["hora_fin"] ?? 'N/A' ?></td>
                        <td><?= $fila["estado_actividad"] ?></td>
                        <td><?= $fila["descripcion_actividad"] ?></td>
                    </tr>
                <?php endforeach; // Fin del bucle. ?>
            <?php 
            // Si no se encontraron actividades, se muestra una fila con un mensaje.
            else: 
            ?>
                <tr>
                    <td colspan="7">No hay datos para exportar con los filtros seleccionados.</td>
                </tr>
            <?php endif; // Fin de la condición. ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Envía todo el contenido HTML capturado en el buffer al navegador.
ob_end_flush();
// Detiene la ejecución del script para asegurar que no se envíe nada más.
exit;
?>