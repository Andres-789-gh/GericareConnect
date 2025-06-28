use gericare_connect;

-- consulta 1: perfil del paciente con historia clínica
select
    p.id_paciente,
    p.documento_identificacion,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente,
    p.fecha_nacimiento,
    p.genero,
    concat(uf.nombre, ' ', uf.apellido) as familiar_contacto,
    uf.parentesco as familiar_parentesco,
    (select tfn.numero_telefono from tb_telefono tfn where tfn.id_usuario = uf.id_usuario and tfn.estado = 'Activo' limit 1) as familiar_telefono,
    concat(uc_asignado.nombre, ' ', uc_asignado.apellido) as cuidador_asignado,
    pa.descripcion as descripcion_asignacion_cuidador,
    hc.id_historia_clinica,
    hc.estado_salud,
    hc.condiciones as condiciones_generales_hc,
    hc.dietas_especiales,
    hc.fecha_ultima_consulta,
    concat(u_cuid_hc.nombre, ' ', u_cuid_hc.apellido) as hc_gestionada_por,
    group_concat(distinct m.nombre_medicamento separator ', ') as medicamentos_recetados,
    group_concat(distinct e.nombre_enfermedad separator ', ') as enfermedades_diagnosticadas
from
    tb_paciente p
left join tb_usuario uf on p.id_usuario_familiar = uf.id_usuario and uf.estado = 'Activo'
left join tb_paciente_asignado pa on p.id_paciente = pa.id_paciente and pa.estado = 'Activo'
left join tb_usuario uc_asignado on pa.id_usuario_cuidador = uc_asignado.id_usuario and uc_asignado.estado = 'Activo'
left join tb_historia_clinica hc on p.id_paciente = hc.id_paciente and hc.estado = 'Activo'
left join tb_usuario u_cuid_hc on hc.id_usuario_cuidador = u_cuid_hc.id_usuario and u_cuid_hc.estado = 'Activo'
left join tb_historia_clinica_medicamento hcm on hc.id_historia_clinica = hcm.id_historia_clinica and hcm.estado = 'Activo'
left join tb_medicamento m on hcm.id_medicamento = m.id_medicamento and m.estado = 'Activo'
left join tb_historia_clinica_enfermedad hce on hc.id_historia_clinica = hce.id_historia_clinica and hce.estado = 'Activo'
left join tb_enfermedad e on hce.id_enfermedad = e.id_enfermedad and e.estado = 'Activo'
where
    p.estado = 'Activo'
group by
    p.id_paciente, hc.id_historia_clinica
order by
    p.nombre asc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 2: actividades y tratamientos de un paciente específico
select
    p.id_paciente,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente,
    t.descripcion as descripcion_tratamiento,
    t.fecha_inicio as inicio_tratamiento,
    t.fecha_fin as fin_tratamiento,
    t.estado_tratamiento,
    a.tipo_actividad,
    a.fecha_actividad,
    a.hora_inicio as inicio_actividad,
    a.hora_fin as fin_actividad,
    a.estado_actividad
from
    tb_paciente p
left join tb_tratamiento t on p.id_paciente = t.id_paciente and t.estado_tratamiento != 'Cancelado'
left join tb_actividad a on p.id_paciente = a.id_paciente and a.estado_actividad != 'Cancelada'
where
    p.id_paciente = 1 -- id del paciente
    and p.estado = 'Activo'
order by
    p.nombre, t.fecha_inicio desc, a.fecha_actividad desc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 3: información de los cuidadores y sus turnos asignados
select
    u.id_usuario as id_cuidador,
    u.documento_identificacion,
    concat(u.nombre, ' ', u.apellido) as nombre_cuidador,
    u.correo_electronico,
    u.fecha_contratacion,
    u.tipo_contrato,
    (select tf.numero_telefono from tb_telefono tf where tf.id_usuario = u.id_usuario and tf.estado = 'Activo' limit 1) as telefono_cuidador,
    group_concat(distinct concat(t.dia_de_la_semana, ' (', time_format(t.hora_inicio, '%h:%i %p'), ' - ', time_format(t.hora_fin, '%h:%i %p'), ')') order by t.dia_de_la_semana, t.hora_inicio separator ', ') as turnos_asignados,
    group_concat(distinct concat(p.nombre, ' ', p.apellido, ' (', pa.descripcion, ')') separator '; ') as pacientes_asignados
from
    tb_usuario u
inner join tb_usuario_rol ur on u.id_usuario = ur.id_usuario and ur.estado = 'Activo'
inner join tb_rol r on ur.id_rol = r.id_rol and r.nombre_rol = 'Cuidador'
left join tb_turno t on u.id_usuario = t.id_usuario_cuidador and t.estado = 'Activo'
left join tb_paciente_asignado pa on u.id_usuario = pa.id_usuario_cuidador and pa.estado = 'Activo'
left join tb_paciente p on pa.id_paciente = p.id_paciente and p.estado = 'Activo'
where
    u.estado = 'Activo'
group by
    u.id_usuario
order by
    u.nombre asc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 4: detalles de solicitudes pendientes y el administrador a cargo
select
    s.id_solicitud,
    p.nombre as nombre_paciente,
    p.apellido as apellido_paciente,
    uf.nombre as nombre_familiar_solicitante,
    uf.apellido as apellido_familiar_solicitante,
    s.tipo_solicitud,
    s.fecha_solicitud,
    s.urgencia_solicitud,
    s.motivo_solicitud,
    s.estado_solicitud,
    concat(ua.nombre, ' ', ua.apellido) as administrador_encargado
from
    tb_solicitud s
join tb_paciente p on s.id_paciente = p.id_paciente and p.estado = 'Activo'
join tb_usuario uf on s.id_usuario_familiar = uf.id_usuario and uf.estado = 'Activo'
left join tb_usuario ua on s.id_usuario_administrador = ua.id_usuario and ua.estado = 'Activo'
where
    s.estado_solicitud = 'Pendiente'
order by
    s.urgencia_solicitud desc, s.fecha_solicitud asc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 5: historial completo de entradas y salidas de pacientes por cuidador
select
    esp.id_entrada_salida_paciente,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente,
    concat(u_cuid.nombre, ' ', u_cuid.apellido) as nombre_cuidador,
    esp.fecha_entrada_salida_paciente,
    esp.tipo_movimiento,
    esp.motivo_entrada_salida_paciente,
    esp.observaciones
from
    tb_entrada_salida_paciente esp
join tb_paciente p on esp.id_paciente = p.id_paciente
join tb_usuario u_cuid on esp.id_usuario_cuidador = u_cuid.id_usuario
where
    p.estado = 'Activo' and u_cuid.estado = 'Activo'
order by
    esp.fecha_entrada_salida_paciente desc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 6: resumen de roles y usuarios
select
    r.nombre_rol,
    count(ur.id_usuario) as cantidad_usuarios,
    group_concat(concat(u.nombre, ' ', u.apellido) separator ', ') as nombres_usuarios
from
    tb_rol r
left join tb_usuario_rol ur on r.id_rol = ur.id_rol and ur.estado = 'Activo'
left join tb_usuario u on ur.id_usuario = u.id_usuario and u.estado = 'Activo'
group by
    r.nombre_rol
order by
    r.nombre_rol;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 7: pacientes con tratamientos activos y sus cuidadores/administradores asignados
select
    p.id_paciente,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente,
    t.descripcion as descripcion_tratamiento,
    t.fecha_inicio,
    t.fecha_fin,
    t.estado_tratamiento,
    concat(uc.nombre, ' ', uc.apellido) as cuidador_tratamiento,
    concat(ua.nombre, ' ', ua.apellido) as administrador_tratamiento
from
    tb_paciente p
join tb_tratamiento t on p.id_paciente = t.id_paciente
left join tb_usuario uc on t.id_usuario_cuidador = uc.id_usuario
left join tb_usuario ua on t.id_usuario_administrador = ua.id_usuario
where
    t.estado_tratamiento = 'Activo'
    and p.estado = 'Activo'
    and (uc.id_usuario is null or uc.estado = 'Activo')
    and (ua.id_usuario is null or ua.estado = 'Activo')
order by
    p.nombre, t.fecha_inicio;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 8: información de pacientes asignados a un administrador específico
select
    ua.id_usuario as id_administrador,
    concat(ua.nombre, ' ', ua.apellido) as nombre_administrador,
    pa.id_paciente_asignado,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente_asignado,
    pa.descripcion as descripcion_asignacion,
    concat(uc.nombre, ' ', uc.apellido) as nombre_cuidador_asignado
from
    tb_usuario ua
inner join tb_usuario_rol ura on ua.id_usuario = ura.id_usuario and ura.estado = 'Activo'
inner join tb_rol ra on ura.id_rol = ra.id_rol and ra.nombre_rol = 'Administrador'
left join tb_paciente_asignado pa on ua.id_usuario = pa.id_usuario_administrador and pa.estado = 'Activo'
left join tb_paciente p on pa.id_paciente = p.id_paciente and p.estado = 'Activo'
left join tb_usuario uc on pa.id_usuario_cuidador = uc.id_usuario and uc.estado = 'Activo'
where
    ua.id_usuario = 1 -- Reemplaza con el id del administrador deseado
    and ua.estado = 'Activo'
order by 
    pa.id_paciente_asignado desc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 9: información del paciente relacionado al familiar y solicitudes realizadas por el familiar
select
    uf.id_usuario as id_familiar,
    concat(uf.nombre, ' ', uf.apellido) as nombre_familiar,
    uf.parentesco,
    uf.correo_electronico as email_familiar,
    (select tf.numero_telefono from tb_telefono tf where tf.id_usuario = uf.id_usuario and tf.estado = 'Activo' limit 1) as telefono_familiar,
    p.id_paciente,
    concat(p.nombre, ' ', p.apellido) as nombre_paciente_acargo,
    p.fecha_nacimiento as paciente_fecha_nacimiento,
    (select hc.estado_salud from tb_historia_clinica hc where hc.id_paciente = p.id_paciente and hc.estado = 'Activo' order by hc.fecha_ultima_consulta desc limit 1) as paciente_estado_salud,
    s.id_solicitud,
    s.tipo_solicitud,
    s.fecha_solicitud,
    s.motivo_solicitud as motivo_de_la_solicitud,
    s.urgencia_solicitud,
    s.estado_solicitud,
    concat(ua.nombre, ' ', ua.apellido) as solicitud_gestionada_por_admin
from
    tb_usuario uf
inner join tb_usuario_rol ur on uf.id_usuario = ur.id_usuario and ur.estado = 'Activo'
inner join tb_rol r on ur.id_rol = r.id_rol and r.nombre_rol = 'Familiar'
left join tb_paciente p on uf.id_usuario = p.id_usuario_familiar and p.estado = 'Activo'
left join tb_solicitud s on p.id_paciente = s.id_paciente and uf.id_usuario = s.id_usuario_familiar and s.estado_solicitud != 'Cancelada'
left join tb_usuario ua on s.id_usuario_administrador = ua.id_usuario and ua.estado = 'Activo'
where
    uf.estado = 'Activo'
order by
    uf.nombre, p.nombre, s.fecha_solicitud desc;

-- ------------------------------------------------------------------------------------------------------------------

-- consulta 10: medicamentos y enfermedades frecuentes en pacientes
select
    m.nombre_medicamento,
    count(hcm.id_medicamento) as veces_recetado
from
    tb_medicamento m
join tb_historia_clinica_medicamento hcm on m.id_medicamento = hcm.id_medicamento
join tb_historia_clinica hc on hcm.id_historia_clinica = hc.id_historia_clinica
join tb_paciente p on hc.id_paciente = p.id_paciente
where
    m.estado = 'Activo'
    and hcm.estado = 'Activo'
    and hc.estado = 'Activo'
    and p.estado = 'Activo'
group by
    m.nombre_medicamento
order by
    veces_recetado desc
limit 5;

select
    e.nombre_enfermedad,
    count(hce.id_enfermedad) as veces_diagnosticada
from
    tb_enfermedad e
join tb_historia_clinica_enfermedad hce on e.id_enfermedad = hce.id_enfermedad
join tb_historia_clinica hc on hce.id_historia_clinica = hc.id_historia_clinica
join tb_paciente p on hc.id_paciente = p.id_paciente
where
    e.estado = 'Activo'
    and hce.estado = 'Activo'
    and hc.estado = 'Activo'
    and p.estado = 'Activo'
group by
    e.nombre_enfermedad
order by
    veces_diagnosticada desc
limit 5;