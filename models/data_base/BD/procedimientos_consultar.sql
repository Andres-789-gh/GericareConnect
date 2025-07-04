/* familiar - pacientes relacionados a un familiar */
delimiter //
create procedure consultar_pacientes_familiar(
    in p_id_familiar int,
    in p_busqueda varchar(100)
)
begin
    select
        p.id_paciente,
        p.documento_identificacion,
        p.nombre,
        p.apellido,
        p.fecha_nacimiento,
        p.genero,
        p.estado
    from
        tb_paciente as p
    where
        p.id_usuario_familiar = p_id_familiar and p.estado = 'Activo'
        and (
            p_busqueda is null or p_busqueda = '' or
            p.nombre like concat('%', p_busqueda, '%') or
            p.apellido like concat('%', p_busqueda, '%') or
            p.documento_identificacion like concat('%', p_busqueda, '%')
        );
end //
delimiter ;

/* familiar - solicitudes echas por el familiar */
delimiter //
create procedure consultar_solicitudes_familiar(
    in p_id_familiar int,
    in p_busqueda varchar(100)
)
begin
    select
        s.id_solicitud,
        s.tipo_solicitud,
        s.motivo_solicitud,
        s.estado_solicitud,
        s.fecha_solicitud
    from
        tb_solicitud as s
    where
        s.id_usuario_familiar = p_id_familiar
        and (
            p_busqueda is null or p_busqueda = '' or
            s.tipo_solicitud like concat('%', p_busqueda, '%') or
            s.motivo_solicitud like concat('%', p_busqueda, '%') or
            s.id_solicitud like concat('%', p_busqueda, '%')
        );
end //
delimiter ;

/* cuidador - pacientes asignados */
delimiter //
create procedure consultar_pacientes_cuidador(
    in p_id_cuidador int,
    in p_busqueda varchar(100)
)
begin
    select
        p.id_paciente,
        p.documento_identificacion,
        p.nombre,
        p.apellido,
        p.fecha_nacimiento,
        p.genero
    from
        tb_paciente_asignado as pa
    join
        tb_paciente as p on pa.id_paciente = p.id_paciente
    where
        pa.id_usuario_cuidador = p_id_cuidador
        and pa.estado = 'Activo'
        and p.estado = 'Activo'
        and (
            p_busqueda is null or p_busqueda = '' or
            p.nombre like concat('%', p_busqueda, '%') or
            p.apellido like concat('%', p_busqueda, '%') or
            p.documento_identificacion like concat('%', p_busqueda, '%')
        );
end //
delimiter ;


delimiter //

create procedure admin_consulta_global(
    in p_filtro_tipo varchar(50),
    in p_busqueda varchar(100),
    in p_id_admin_actual int
)
begin
    -- si se filtra por un rol de usuario
    if p_filtro_tipo in ('Familiar', 'Cuidador', 'Administrador') then
        select
            u.id_usuario as id, 'Usuario' as tipo_entidad, u.documento_identificacion as documento,
            concat(u.nombre, ' ', u.apellido) as nombre_completo, r.nombre_rol as rol, u.correo_electronico as contacto
        from tb_usuario as u
        join tb_rol as r on u.id_rol = r.id_rol
        where
            r.nombre_rol = p_filtro_tipo
            and u.estado = 'Activo'
            and u.id_usuario != p_id_admin_actual
            and (
                p_busqueda is null or p_busqueda = '' or
                u.nombre like concat('%', p_busqueda, '%') or
                u.apellido like concat('%', p_busqueda, '%') or
                u.documento_identificacion like concat('%', p_busqueda, '%')
            );
    -- si se filtra por paciente
    elseif p_filtro_tipo = 'Paciente' then
        select
            p.id_paciente as id, 'Paciente' as tipo_entidad, p.documento_identificacion as documento,
            concat(p.nombre, ' ', p.apellido) as nombre_completo, 'Paciente' as rol, p.contacto_emergencia as contacto
        from tb_paciente as p
        where
            p.estado = 'Activo'
            and (
                p_busqueda is null or p_busqueda = '' or
                p.nombre like concat('%', p_busqueda, '%') or
                p.apellido like concat('%', p_busqueda, '%') or
                p.documento_identificacion like concat('%', p_busqueda, '%')
            );
    -- si no hay filtro de tipo, busca en todos los activos
    else
        (select
            u.id_usuario as id, 'Usuario' as tipo_entidad, u.documento_identificacion as documento,
            concat(u.nombre, ' ', u.apellido) as nombre_completo, r.nombre_rol as rol, u.correo_electronico as contacto
        from tb_usuario as u
        join tb_rol as r on u.id_rol = r.id_rol
        where
            r.nombre_rol != 'Administrador'
            and u.estado = 'Activo' -- <<< CORRECCIÓN AÑADIDA
            and (
                p_busqueda is null or p_busqueda = '' or
                u.nombre like concat('%', p_busqueda, '%') or
                u.apellido like concat('%', p_busqueda, '%') or
                u.documento_identificacion like concat('%', p_busqueda, '%')
            )
        )
        union all
        (select
            p.id_paciente as id, 'Paciente' as tipo_entidad, p.documento_identificacion as documento,
            concat(p.nombre, ' ', p.apellido) as nombre_completo, 'Paciente' as rol, p.contacto_emergencia as contacto
        from tb_paciente as p
        where
            p.estado = 'Activo'
            and (
                p_busqueda is null or p_busqueda = '' or
                p.nombre like concat('%', p_busqueda, '%') or
                p.apellido like concat('%', p_busqueda, '%') or
                p.documento_identificacion like concat('%', p_busqueda, '%')
            )
        );
    end if;
end //
delimiter ;