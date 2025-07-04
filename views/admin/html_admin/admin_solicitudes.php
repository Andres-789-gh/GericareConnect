<?php
require_once __DIR__ . '/../../../controllers/auth/verificar_sesion.php';
verificarAcceso(['Administrador']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Solicitudes - GeriCare Connect</title>
    <link rel="stylesheet" href="../../admin/css_admin/admin_solicitudes.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .detalle-solicitud-contenido { text-align: left; line-height: 1.6; max-height: 60vh; overflow-y: auto; padding-right: 15px;}
        .detalle-solicitud-contenido h4 { margin-top: 15px; margin-bottom: 5px; color: #0056b3; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 5px;}
        .detalle-solicitud-contenido p { margin-bottom: 8px;}
        .detalle-solicitud-contenido strong { color: #333; }
        .detalle-solicitud-contenido pre { background-color: #f8f9fa; border: 1px solid #eee; padding: 10px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; font-size: 0.95em; max-height: 150px; overflow-y: auto;}
        .detalle-solicitud-contenido .respuesta-admin-area textarea { width: 98%; min-height: 70px; margin-top: 5px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.95em;}
        .estado-base { padding: 4px 10px; border-radius: 15px; font-size: 0.85em; font-weight: 500; border: 1px solid transparent; }
        .estado-pendiente { background-color: #fff3cd; color: #85640a; border-color: #ffeeba; }
        .estado-aprobada { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .estado-rechazada { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .estado-procesada, .estado-completada { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .swal-button { padding: 8px 15px !important; font-size: 0.9em !important; margin: 0 5px !important; border-radius: 5px !important; }
        .swal-button-agregar { background-color: #28a745 !important; color: white !important; }
        .swal-button-eliminar { background-color: #dc3545 !important; color: white !important; }
        .swal-button-responder { background-color: #007bff !important; color: white !important; }
        .swal-footer-custom { display: flex; justify-content: center; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;}
        .solicitud-item { display: flex; justify-content: space-between; align-items: center; gap: 15px; cursor: pointer; }
        .solicitud-col-principal { display: flex; align-items: center; gap: 10px; flex-basis: 30%; }
        .solicitud-icono { font-size: 1.3em; color: #6c757d; }
        .solicitud-fecha { font-size: 0.85em; color: #6c757d; }
        .solicitud-col-familiar { flex-basis: 40%; font-size: 0.9em; }
        .solicitud-email { color: #007bff; font-size: 0.9em; }
        .solicitud-col-estado { flex-basis: 20%; text-align: right; }
        .solicitud-item.no-data, .solicitud-item.cargando, .solicitud-item.error { display: block; text-align: center; cursor: default;}
    </style>
</head>
<body>
    <header class="admin-header animated fadeInDown">
        <div class="logo-container">
            <img src="../../imagenes/Geri_Logo-..png" alt="Logo" class="logo" onclick="window.location.href='admin_pacientes.html'">
            <span class="app-name">GERICARE CONNECT</span>
        </div>
        <nav>
            <ul>
                <li><a href="admin_pacientes.html"><i class="fas fa-user-injured"></i> Pacientes</a></li>
                <li><a href="admin_solicitudes.php" class="active"><i class="fas fa-envelope-open-text"></i> Solicitudes</a></li>
                <li><a href="../../../controllers/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main class="admin-content">
        <div class="solicitudes-container animated fadeInUp">
            <h1 class="animated slideInLeft"><i class="fas fa-envelope-open-text"></i> Administrar Solicitudes</h1>
            <div class="search-container animated slideInRight">
                <form id="buscarSolicitudesForm" method="GET" action="javascript:void(0);">
                    <input type="search" id="buscar-solicitud" name="buscar-solicitud" placeholder="Buscar por ID, tipo, estado, familiar...">
                    <button type="submit" class="search-button" title="Buscar"><i class="fas fa-search"></i></button>
                    <button type="button" class="clear-button" id="clear-search-button" title="Limpiar Búsqueda"><i class="fas fa-times"></i></button>
                </form>
            </div>
            <ul class="solicitud-list" id="solicitud-list">
                <li class="solicitud-item cargando"><i class="fas fa-spinner fa-spin"></i> Cargando solicitudes...</li>
            </ul>
        </div>
    </main>

    <script src="../../admin/js_admin/admin_solicitudes_vista.js"></script>
    <script>
          function confirmarYEliminarPaciente(pacienteId) {
              Swal.fire({
                  title: '¿Estás seguro?',
                  text: `¿Realmente deseas eliminar al paciente con ID ${pacienteId}? Esta acción no se puede deshacer.`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Sí, eliminar',
                  cancelButtonText: 'Cancelar',
                  zIndex: 1060 
              }).then((result) => {
                  if (result.isConfirmed) {
                      ejecutarEliminacionPacienteAdmin(pacienteId);
                  }
              });
          }

          function ejecutarEliminacionPacienteAdmin(pacienteId) {
              
               Swal.fire({title: 'Eliminando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }, zIndex: 1070});

               fetch('../../../controllers/admin/admin_pacientes_eliminar.php', {
                   method: 'POST',
                   headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                   body: `paciente_id=${pacienteId}`
               })
               .then(response => response.json())
               .then(data => {
                   Swal.fire({
                        title: data.success ? '¡Eliminado!' : 'Error al Eliminar',
                        text: data.message,
                        icon: data.success ? 'success' : 'error',
                        zIndex: 1070
                   })
               })
               .catch(error => {
                    console.error("Error fetch eliminar:", error);
                    Swal.fire({title: 'Error de Red', text:'No se pudo eliminar el paciente.', icon:'error', zIndex: 1070});
                });
          }
    </script>

</body>
</html>