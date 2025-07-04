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
    -- 1. Desactivar el paciente principal
    update tb_paciente set estado = 'Inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 2. Desactivar las asignaciones del paciente
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 3. Desactivar la historia clínica del paciente
    update tb_historia_clinica set estado = 'Inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 4. Actualizar tratamientos activos a 'Finalizado' o 'Cancelado'
    update tb_tratamiento set estado_tratamiento = 'Finalizado' where id_paciente = p_id_paciente_a_desactivar AND estado_tratamiento = 'Activo';

    -- 5. Cancelar actividades pendientes
    update tb_actividad set estado_actividad = 'Cancelada' where id_paciente = p_id_paciente_a_desactivar AND estado_actividad = 'Pendiente';

    -- 6. Cancelar solicitudes pendientes
    update tb_solicitud set estado_solicitud = 'Cancelada' where id_paciente = p_id_paciente_a_desactivar AND estado_solicitud = 'Pendiente';

end //
/*create procedure desactivar_paciente(
    in p_id_paciente_a_desactivar int
)
begin
    update tb_paciente set estado = 'Inactivo' where id_paciente = p_id_paciente_a_desactivar;
    -- desactivar asignaciones, etc
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente = p_id_paciente_a_desactivar;
end //*/
delimiter ;
