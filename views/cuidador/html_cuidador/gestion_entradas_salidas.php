<?php
session_start();
// Incluir modelos para obtener listas de pacientes (aun no la tenemos)
require_once(__DIR__ . '/../../models/clases/entrada_salida.php');

$entrada_salida_modelo = new entradaSalida();
$historial = $entrada_salida_modelo->consultar();

/*
require_once(__DIR__ . '/../../models/clases/pacientes.php');
$paciente_modelo = new Paciente();
$lista_pacientes = $paciente_modelo->consultarTodos(); 
$lista_pacientes = []; */

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Entradas y Salidas</title>
    <link rel="stylesheet" href="/GericareConnect/views/css/styles.css"> </head>
<body>
    <div class="container">
        <h1>Registro de Entradas y Salidas</h1>

        <?php
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="mensaje-exito">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
            unset($_SESSION['mensaje']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="mensaje-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <div class="form-container">
            <h2><i class="fas fa-plus-circle"></i> Nuevo Registro</h2>
            <form action="/GericareConnect/controllers/entrada_salida_controller.php" method="POST">
                <input type="hidden" name="accion" value="registrar">
                
                <div class="form-group">
                    <label for="id_paciente">Paciente:</label>
                    <select name="id_paciente" required>
                        <option value="">-- Seleccione un Paciente --</option>
                        <?php foreach ($lista_pacientes as $paciente): ?>
                            <option value="<?= $paciente['id_paciente'] ?>"><?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_movimiento">Tipo de Movimiento:</label>
                    <select name="tipo_movimiento" required>
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo:</label>
                    <input type="text" name="motivo" placeholder="Ej: Cita médica, Ingreso inicial..." required>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones (Opcional):</label>
                    <textarea name="observaciones" placeholder="Añadir detalles relevantes..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Registro</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2><i class="fas fa-history"></i> Historial de Movimientos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Paciente</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th>Observaciones</th>
                        <th>Registrado por</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historial)): ?>
                        <?php foreach ($historial as $reg): ?>
                            <tr>
                                <td><?= htmlspecialchars($reg['id_entrada_salida_paciente']) ?></td>
                                <td><?= htmlspecialchars(date("d/m/Y h:i A", strtotime($reg['fecha_entrada_salida_paciente']))) ?></td>
                                <td><?= htmlspecialchars($reg['nombre_paciente']) ?></td>
                                <td><?= htmlspecialchars($reg['tipo_movimiento']) ?></td>
                                <td><?= htmlspecialchars($reg['motivo_entrada_salida_paciente']) ?></td>
                                <td><?= nl2br(htmlspecialchars($reg['observaciones'])) ?></td>
                                <td><?= htmlspecialchars($reg['nombre_cuidador']) ?></td>
                                <td>
                                    <button class="btn btn-warning" onclick="abrirModalActualizar(<?= $reg['id_entrada_salida_paciente'] ?>, `<?= htmlspecialchars(addslashes($reg['observaciones'])) ?>`)">Editar Obs.</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No hay registros de entradas o salidas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-actualizar" style="display:none;">
        <form action="/GericareConnect/controllers/entrada_salida_controller.php" method="POST">
            <h3>Actualizar Observaciones</h3>
            <input type="hidden" name="accion" value="actualizar_obs">
            <input type="hidden" id="modal_id_registro" name="id_registro">
            <textarea id="modal_observaciones" name="observaciones" rows="5"></textarea>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
        </form>
    </div>

    <script>
        function abrirModalActualizar(id, observaciones) {
            document.getElementById('modal_id_registro').value = id;
            document.getElementById('modal_observaciones').value = observaciones;
            document.getElementById('modal-actualizar').style.display = 'block';
        }
        function cerrarModal() {
            document.getElementById('modal-actualizar').style.display = 'none';
        }
    </script>
</body>
</html>