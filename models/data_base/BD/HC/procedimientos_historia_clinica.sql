-- usar la base de datos
use gericare_connect;

-- eliminar procedimientos
drop procedure if exists registrar_historia_clinica;
drop procedure if exists consultar_historia_clinica;
drop procedure if exists actualizar_historia_clinica;
drop procedure if exists eliminar_historia_clinica;
drop procedure if exists consultar_enfermedades_hc;
drop procedure if exists asignar_enfermedad_hc;
drop procedure if exists eliminar_enfermedad_hc;
drop procedure if exists consultar_medicamentos_hc;
drop procedure if exists asignar_medicamento_hc;
drop procedure if exists actualizar_medicamento_hc;
drop procedure if exists eliminar_medicamento_hc;
drop procedure if exists consultar_reporte_completo_hc;

delimiter $$

-- crud básico de historia clínica

create procedure registrar_historia_clinica(in p_id_paciente int, in p_id_usuario_administrador int, in p_estado_salud text, in p_condiciones text, in p_antecedentes_medicos text, in p_alergias text, in p_dietas_especiales text, in p_fecha_ultima_consulta date, in p_observaciones text)
begin
    insert into tb_historia_clinica (id_paciente, id_usuario_administrador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado)
    values (p_id_paciente, p_id_usuario_administrador, p_estado_salud, p_condiciones, p_antecedentes_medicos, p_alergias, p_dietas_especiales, p_fecha_ultima_consulta, p_observaciones, 'Activo');
end$$

-- cuenta las asignaciones para el "botón inteligente"
create procedure consultar_historia_clinica(in p_id_historia_clinica int, in p_busqueda varchar(100))
begin
    if p_id_historia_clinica is not null then
        select hc.*, concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo
        from tb_historia_clinica as hc
        join tb_paciente as p on hc.id_paciente = p.id_paciente
        where hc.id_historia_clinica = p_id_historia_clinica;
    else
        select
            hc.id_historia_clinica,
            concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo,
            date_format(hc.fecha_ultima_consulta, '%d/%m/%Y') as fecha_formateada,
            hc.estado_salud,
            (select count(*) from tb_historia_clinica_medicamento where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as med_count,
            (select count(*) from tb_historia_clinica_enfermedad where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as enf_count
        from tb_historia_clinica as hc
        join tb_paciente as p on hc.id_paciente = p.id_paciente
        where hc.estado = 'Activo'
            and (p_busqueda is null or p_busqueda = '' or
                 p.nombre like concat('%', p_busqueda, '%') or
                 p.apellido like concat('%', p_busqueda, '%') or
                 p.documento_identificacion like concat('%', p_busqueda, '%'))
        order by hc.id_historia_clinica desc;
    end if;
end$$

create procedure actualizar_historia_clinica(in p_id_historia_clinica int, in p_id_usuario_administrador int, in p_estado_salud text, in p_condiciones text, in p_antecedentes_medicos text, in p_alergias text, in p_dietas_especiales text, in p_fecha_ultima_consulta date, in p_observaciones text)
begin
    update tb_historia_clinica set id_usuario_administrador = p_id_usuario_administrador, estado_salud = p_estado_salud, condiciones = p_condiciones, antecedentes_medicos = p_antecedentes_medicos, alergias = p_alergias, dietas_especiales = p_dietas_especiales, fecha_ultima_consulta = p_fecha_ultima_consulta, observaciones = p_observaciones
    where id_historia_clinica = p_id_historia_clinica;
end$$

create procedure eliminar_historia_clinica(in p_id_historia_clinica int)
begin
    update tb_historia_clinica set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
end$$

-- gestión de asignaciones

create procedure consultar_enfermedades_hc(in p_id_historia_clinica int)
begin
    select hce.id_hc_enfermedad, e.id_enfermedad, e.nombre_enfermedad from tb_historia_clinica_enfermedad as hce join tb_enfermedad as e on hce.id_enfermedad = e.id_enfermedad where hce.id_historia_clinica = p_id_historia_clinica and hce.estado = 'Activo';
end$$

create procedure asignar_enfermedad_hc(in p_id_historia_clinica int, in p_id_enfermedad int)
begin
    if not exists (select 1 from tb_historia_clinica_enfermedad where id_historia_clinica = p_id_historia_clinica and id_enfermedad = p_id_enfermedad and estado = 'Activo') then
        insert into tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, fecha_diagnostico, estado) values (p_id_historia_clinica, p_id_enfermedad, curdate(), 'Activo');
        select last_insert_id() as id_asignacion;
    else
        select 0 as id_asignacion;
    end if;
end$$

create procedure eliminar_enfermedad_hc(in p_id_hc_enfermedad int)
begin
    delete from tb_historia_clinica_enfermedad where id_hc_enfermedad = p_id_hc_enfermedad;
end$$

create procedure consultar_medicamentos_hc(in p_id_historia_clinica int)
begin
    select hcm.id_hc_medicamento, m.id_medicamento, m.nombre_medicamento, hcm.dosis, hcm.frecuencia, hcm.instrucciones from tb_historia_clinica_medicamento as hcm join tb_medicamento as m on hcm.id_medicamento = m.id_medicamento where hcm.id_historia_clinica = p_id_historia_clinica and hcm.estado = 'Activo';
end$$

create procedure asignar_medicamento_hc(in p_id_historia_clinica int, in p_id_medicamento int, in p_dosis varchar(100), in p_frecuencia varchar(100), in p_instrucciones varchar(250))
begin
    insert into tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, dosis, frecuencia, instrucciones, estado) values (p_id_historia_clinica, p_id_medicamento, p_dosis, p_frecuencia, p_instrucciones, 'Activo');
    select last_insert_id() as id_asignacion;
end$$

create procedure actualizar_medicamento_hc(in p_id_hc_medicamento int, in p_dosis varchar(100), in p_frecuencia varchar(100), in p_instrucciones varchar(250))
begin
    update tb_historia_clinica_medicamento set dosis = p_dosis, frecuencia = p_frecuencia, instrucciones = p_instrucciones where id_hc_medicamento = p_id_hc_medicamento;
end$$

create procedure eliminar_medicamento_hc(in p_id_hc_medicamento int)
begin
    delete from tb_historia_clinica_medicamento where id_hc_medicamento = p_id_hc_medicamento;
end$$

-- procedimiento para el reporte completo
create procedure consultar_reporte_completo_hc(in p_id_historia_clinica int)
begin
    select
        -- datos de la historia
        hc.*,
        -- datos del paciente
        p.nombre as paciente_nombre,
        p.apellido as paciente_apellido,
        p.documento_identificacion as paciente_documento,
        p.fecha_nacimiento as paciente_fecha_nacimiento,
        -- datos del administrador que la gestionó
        u.nombre as admin_nombre,
        u.apellido as admin_apellido
    from
        tb_historia_clinica as hc
    join
        tb_paciente as p on hc.id_paciente = p.id_paciente
    left join
        tb_usuario as u on hc.id_usuario_administrador = u.id_usuario
    where
        hc.id_historia_clinica = p_id_historia_clinica;
end$$

delimiter ;

/*buscar desde el view de cuidador*/
delimiter $$

create procedure consultar_historias_cuidador(in p_id_cuidador int, in p_busqueda varchar(100))
begin
    select
        hc.id_historia_clinica,
        concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo,
        date_format(hc.fecha_ultima_consulta, '%d/%m/%y') as fecha_formateada,
        hc.estado_salud,
        (select count(*) from tb_historia_clinica_medicamento where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as med_count,
        (select count(*) from tb_historia_clinica_enfermedad where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as enf_count
    from
        tb_paciente_asignado pa
    join
        tb_historia_clinica hc on pa.id_paciente = hc.id_paciente
    join
        tb_paciente p on pa.id_paciente = p.id_paciente
    where
        pa.id_usuario_cuidador = p_id_cuidador
        and pa.estado = 'Activo'
        and hc.estado = 'Activo'
        and (p_busqueda is null or p_busqueda = '' or
             p.nombre like concat('%', p_busqueda, '%') or
             p.apellido like concat('%', p_busqueda, '%') or
             p.documento_identificacion like concat('%', p_busqueda, '%'));
end$$

delimiter ;