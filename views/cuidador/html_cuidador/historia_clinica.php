<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
require_once __DIR__ . '/../../../models/clases/historia_clinica.php';

verificarAcceso(['Cuidador']);

$modelo = new HistoriaClinica();
$busqueda = $_GET['busqueda'] ?? '';
// Usamos la nueva función del modelo, pasando el ID del cuidador desde la sesión
$historias = $modelo->consultarHistoriasPorCuidador($_SESSION['id_usuario'], $busqueda);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Historias Clínicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../../admin/css_admin/historia_clinica_lista.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='cuidadores_panel_principal.php'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="cuidadores_panel_principal.php"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="historia_clinica.php" class="active"><i class="fas fa-file-medical"></i> Historias Clínicas</a></li>
                <li><a href="../../../controllers/cuidador/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-content">
        <div class="historias-container">
            <h1><i class="fas fa-book-medical"></i> Historias Clínicas de Pacientes Asignados</h1>
            <div class="search-container">
                <form method="GET" action="historia_clinica.php">
                    <input type="search" name="busqueda" placeholder="Buscar por nombre o documento del paciente..." value="<?= htmlspecialchars($busqueda) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Última Consulta</th>
                            <th>Estado General</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historias)): ?>
                            <tr><td colspan="5">No se encontraron historias clínicas para tus pacientes asignados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($historias as $historia): ?>
                                <tr>
                                    <td><?= htmlspecialchars($historia['id_historia_clinica']) ?></td>
                                    <td><?= htmlspecialchars($historia['paciente_nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($historia['fecha_formateada']) ?></td>
                                    <td><?= htmlspecialchars(substr($historia['estado_salud'], 0, 50)) . '...' ?></td>
                                    <td class="actions">
                                        <a href="../../admin/html_admin/reporte_hc_completo.php?id=<?= $historia['id_historia_clinica'] ?>" class="btn-action btn-info" title="Ver Reporte Completo">
                                            <i class="fas fa-eye"></i> Ver Reporte
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>