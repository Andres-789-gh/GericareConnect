/*
drop procedure registrar_historia_clinica;
drop procedure consultar_historia_clinica;
drop procedure actualizar_historia_clinica;
drop procedure eliminar_historia_clinica;
drop procedure obtener_enfermedades_por_historia;
drop procedure obtener_medicamentos_por_historia;
*/
delimiter $$

-- =============================================
-- procedimiento para registrar una historia clínica
-- =============================================
create procedure registrar_historia_clinica(
    in p_id_paciente int,
    in p_id_usuario_administrador int,
    in p_estado_salud text,
    in p_condiciones text,
    in p_antecedentes_medicos text,
    in p_alergias text,
    in p_dietas_especiales text,
    in p_fecha_ultima_consulta date,
    in p_observaciones text
)
begin
    insert into tb_historia_clinica (
        id_paciente,
        id_usuario_administrador,
        estado_salud,
        condiciones,
        antecedentes_medicos,
        alergias,
        dietas_especiales,
        fecha_ultima_consulta,
        observaciones,
        estado
    )
    values (
        p_id_paciente,
        p_id_usuario_administrador,
        p_estado_salud,
        p_condiciones,
        p_antecedentes_medicos,
        p_alergias,
        p_dietas_especiales,
        p_fecha_ultima_consulta,
        p_observaciones,
        'Activo' -- por defecto, se crea como Activo
    );
end$$

-- =============================================
-- procedimiento para consultar historias clínicas
-- se puede consultar por id de historia o por id de paciente
-- =============================================
create procedure consultar_historia_clinica(
    in p_id_historia_clinica int,
    in p_id_paciente int
)
begin
    if p_id_historia_clinica is not null then
        -- búsqueda por id de la historia clínica
        select * from tb_historia_clinica where id_historia_clinica = p_id_historia_clinica;
    elseif p_id_paciente is not null then
        -- búsqueda por id del paciente (generalmente se busca la activa)
        select * from tb_historia_clinica where id_paciente = p_id_paciente and estado = 'Activo';
    end if;
end$$

-- =============================================
-- procedimiento para actualizar una historia clínica
-- =============================================
create procedure actualizar_historia_clinica(
    in p_id_historia_clinica int,
    in p_id_usuario_administrador int,
    in p_estado_salud text,
    in p_condiciones text,
    in p_antecedentes_medicos text,
    in p_alergias text,
    in p_dietas_especiales text,
    in p_fecha_ultima_consulta date,
    in p_observaciones text,
    in p_estado enum('Activo','Inactivo')
)
begin
    update tb_historia_clinica
    set
        id_usuario_administrador = p_id_usuario_administrador,
        estado_salud = p_estado_salud,
        condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos,
        alergias = p_alergias,
        dietas_especiales = p_dietas_especiales,
        fecha_ultima_consulta = p_fecha_ultima_consulta,
        observaciones = p_observaciones,
        estado = p_estado
    where
        id_historia_clinica = p_id_historia_clinica;
end$$

-- =============================================
-- procedimiento para eliminar (lógicamente) una historia clínica
-- =============================================
create procedure eliminar_historia_clinica(
    in p_id_historia_clinica int
)
begin
    -- se realiza una eliminación lógica cambiando el estado a 'Inactivo'
    update tb_historia_clinica
    set estado = 'Inactivo'
    where id_historia_clinica = p_id_historia_clinica;
end$$

delimiter ;

delimiter $$

create procedure obtener_medicamentos_por_historia(
    in p_id_historia_clinica int
)
begin
    select
        m.id_medicamento,
        m.nombre_medicamento,
        m.descripcion_medicamento,
        hcm.dosis,
        hcm.frecuencia,
        hcm.instrucciones
    from
        tb_historia_clinica_medicamento as hcm
    join
        tb_medicamento as m on hcm.id_medicamento = m.id_medicamento
    where
        hcm.id_historia_clinica = p_id_historia_clinica
        and hcm.estado = 'Activo';
end$$

delimiter ;

delimiter $$

create procedure obtener_enfermedades_por_historia(
    in p_id_historia_clinica int
)
begin
    select
        e.id_enfermedad,
        e.nombre_enfermedad,
        e.descripcion_enfermedad,
        hce.fecha_diagnostico,
        hce.observaciones
    from
        tb_historia_clinica_enfermedad as hce
    join
        tb_enfermedad as e on hce.id_enfermedad = e.id_enfermedad
    where
        hce.id_historia_clinica = p_id_historia_clinica
        and hce.estado = 'Activo';
end$$

delimiter ;
