<?php
// --- INICIO DE LÓGICA PHP ---

// Se inician las dependencias y la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../controllers/admin/HC/historia_clinica_controlador.php';
require_once __DIR__ . '/../../../models/clases/pacientes.php';

// Si la página es llamada por el JavaScript con ?accion=buscar
if (isset($_GET['accion']) && $_GET['accion'] == 'buscar') {
    verificarAcceso(['Administrador']); // Seguridad para el endpoint
    header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

    $controlador = new ControladorHistoriaClinica();
    $busqueda = $_GET['busqueda'] ?? '';
    $resultados = $controlador->mostrar('busqueda', $busqueda);
    
    // Si la consulta falla, el modelo devuelve 'false'. Nos aseguramos de devolver un array vacío.
    echo json_encode($resultados ?: []); 
    exit(); // Detenemos el script para no enviar el HTML
}


verificarAcceso(['Administrador']); // Seguridad para la vista
$controlador = new ControladorHistoriaClinica();

// Procesa el formulario de registro si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["accion"]) && $_POST['accion'] == 'registrar') {
    $controlador->registrar();
}
// Procesa la eliminación si se recibe el ID
if (isset($_GET['idHistoriaEliminar'])) {
    $controlador->eliminar();
}

// Obtiene la lista de pacientes para el dropdown del formulario
$paciente_model = new Paciente();
$pacientes_activos = $paciente_model->consultar();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Historias Clínicas</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../css_admin/admin_pacientes.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestión de Historias Clínicas</h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="historia_clinica.php">
                <input type="hidden" name="accion" value="registrar">
                <h2>Registrar Nueva Historia Clínica</h2>
                
                <div class="form-group">
                    <label for="id_paciente">Paciente:</label>
                    <select name="id_paciente" id="id_paciente" class="select2-paciente" required style="width: 100%;">
                        <option value="">Seleccione un paciente...</option>
                        <?php foreach ($pacientes_activos as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-group"><label>Estado de Salud General:</label><textarea name="estado_salud" rows="3"></textarea></div>
                        <div class="form-group"><label>Antecedentes Médicos:</label><textarea name="antecedentes_medicos" rows="2"></textarea></div>
                    </div>
                    <div class="form-column">
                        <div class="form-group"><label>Condiciones Crónicas:</label><textarea name="condiciones" rows="3"></textarea></div>
                        <div class="form-group"><label>Alergias Conocidas:</label><textarea name="alergias" rows="2"></textarea></div>
                    </div>
                </div>

                <div class="form-group"><label>Dietas Especiales:</label><textarea name="dietas_especiales" rows="2"></textarea></div>
                <div class="form-group"><label>Observaciones Adicionales:</label><textarea name="observaciones" rows="4"></textarea></div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Crear Historia Clínica</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Historias Clínicas Registradas</h2>

            <div class="form-group" style="margin-bottom: 20px;">
                <input type="search" id="buscador-historias" placeholder="Buscar por nombre o cédula del paciente..." style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Fecha Creación</th>
                        <th>Estado General</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-historias-body">
                    </tbody>
            </table>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscador = document.getElementById('buscador-historias');
            const tablaBody = document.getElementById('tabla-historias-body');
            let searchTimeout;

            function cargarHistorias(busqueda = '') {
                tablaBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Buscando...</td></tr>';
                
                // La URL del fetch apunta a este mismo archivo
                const fetchUrl = `historia_clinica.php?accion=buscar&busqueda=${encodeURIComponent(busqueda)}`;
                
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        tablaBody.innerHTML = ''; 
                        if (data && data.length > 0) {
                            data.forEach(historia => {
                                const estadoSalud = historia.estado_salud || '';
                                const fila = `
                                    <tr>
                                        <td>${historia.id_historia_clinica}</td>
                                        <td>${historia.paciente_nombre_completo}</td>
                                        <td>${historia.fecha_formateada}</td>
                                        <td title="${estadoSalud}">${estadoSalud.substring(0, 50)}...</td>
                                        <td>
                                            <a href="editar_historia_clinica.php?id=${historia.id_historia_clinica}" class="btn btn-warning">Editar/Ver</a>
                                            <a href="historia_clinica.php?idHistoriaEliminar=${historia.id_historia_clinica}" class="btn btn-danger" onclick="return confirm('¿Estás seguro?');">Eliminar</a>
                                        </td>
                                    </tr>
                                `;
                                tablaBody.innerHTML += fila;
                            });
                        } else {
                            tablaBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No se encontraron historias clínicas.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Error en fetch:', error);
                        tablaBody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error al cargar los datos. Verifique la consola para más detalles.</td></tr>';
                    });
            }

            cargarHistorias();
            buscador.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => cargarHistorias(buscador.value), 400);
            });
            
            $('.select2-paciente').select2({
                placeholder: "Escribe o selecciona un paciente",
                allowClear: true
            });
        });
    </script>
</body>
</html>