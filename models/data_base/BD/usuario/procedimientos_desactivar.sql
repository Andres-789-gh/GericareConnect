-- usar la base de datos correcta
use gericare_connect;

/* administrador - desactivar un usuario (familiar o cuidador) */
delimiter //
create procedure desactivar_usuario(
    in p_id_usuario_a_desactivar int,
    in p_id_admin_actual int
)
begin
    declare v_rol_a_desactivar varchar(50);

    -- verificar que un admin no se desactive a sí mismo
    if p_id_usuario_a_desactivar = p_id_admin_actual then
        signal sqlstate '45000' set message_text = 'Error: Un administrador no puede desactivar su propia cuenta.';
    end if;

    -- obtener el rol del usuario que se quiere desactivar
    select r.nombre_rol into v_rol_a_desactivar
    from tb_usuario u
    join tb_rol r on u.id_rol = r.id_rol
    where u.id_usuario = p_id_usuario_a_desactivar;

    -- verificar que el rol no sea 'administrador'
    if v_rol_a_desactivar = 'Administrador' then
        signal sqlstate '45000' set message_text = 'Error: No está permitido desactivar a otro administrador.';
    else
        -- si no es admin, proceder a desactivar
        update tb_usuario set estado = 'Inactivo' where id_usuario = p_id_usuario_a_desactivar;
        -- también desactivar su teléfono
        update tb_telefono set estado = 'Inactivo' where id_usuario = p_id_usuario_a_desactivar;
    end if;
end //
delimiter ;


/* administrador - desactiva un paciente */
delimiter //
create procedure desactivar_paciente(
    in p_id_paciente_a_desactivar int
)
begin
    declare v_id_historia_clinica int;

    -- iniciar una transacción para asegurar que todas las operaciones se completen o ninguna lo haga
    start transaction;

    -- obtener el id de la historia clínica del paciente si existe
    select id_historia_clinica into v_id_historia_clinica
    from tb_historia_clinica
    where id_paciente = p_id_paciente_a_desactivar and estado = 'activo'
    limit 1;

    -- 1. desactivar el paciente
    update tb_paciente set estado = 'inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 2. desactivar todas las asignaciones del paciente a cuidadores
    update tb_paciente_asignado set estado = 'inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 3. desactivar actividades del paciente
    update tb_actividad set estado_actividad = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_actividad = 'pendiente';
    
    -- 4. desactivar tratamientos del paciente
    update tb_tratamiento set estado_tratamiento = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_tratamiento = 'activo';

    -- 5. desactivar solicitudes relacionadas al paciente
    update tb_solicitud set estado_solicitud = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_solicitud = 'pendiente';

    -- 6. si el paciente tiene una historia clínica junto con sus detalles
    if v_id_historia_clinica is not null then
        -- desactivar la historia clínica principal
        update tb_historia_clinica set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar medicamentos asignados en esa historia
        update tb_historia_clinica_medicamento set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar enfermedades asignadas en esa historia
        update tb_historia_clinica_enfermedad set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar cirugías registradas en esa historia
        update tb_historia_clinica_cirugia set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;
    end if;

    -- confirmar todos los cambios si no hubo errores
    commit;
end//
delimiter ;