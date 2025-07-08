use gericare_connect;

drop procedure if exists registrar_actividad;
drop procedure if exists consultar_actividades;
drop procedure if exists actualizar_actividad;
drop procedure if exists eliminar_actividad;
drop procedure if exists consultar_actividades_cuidador;

delimiter //

-- registrar una nueva actividad
create procedure registrar_actividad(
    in p_id_paciente int,
    in p_id_usuario_administrador int,
    in p_tipo_actividad varchar(100),
    in p_descripcion_actividad text,
    in p_fecha_actividad date,
    in p_hora_inicio time,
    in p_hora_fin time
)
begin
    insert into tb_actividad (
        id_paciente, id_usuario_administrador, tipo_actividad, descripcion_actividad,
        fecha_actividad, hora_inicio, hora_fin, estado_actividad
    ) values (
        p_id_paciente, p_id_usuario_administrador, p_tipo_actividad, p_descripcion_actividad,
        p_fecha_actividad, p_hora_inicio, p_hora_fin, 'pendiente'
    );
end//

-- consultar actividades (admin)
create procedure consultar_actividades(
    in p_busqueda varchar(100),
    in p_estado_filtro varchar(20)
)
begin
    select
        a.id_actividad, a.tipo_actividad, a.descripcion_actividad,
        a.fecha_actividad, a.hora_inicio, a.hora_fin,
        a.estado_actividad, p.id_paciente,
        concat(p.nombre, ' ', p.apellido) as nombre_paciente
    from tb_actividad a
    join tb_paciente p on a.id_paciente = p.id_paciente
    where
        a.estado_actividad != 'inactivo'
        and (p_estado_filtro is null or p_estado_filtro = '' or a.estado_actividad = p_estado_filtro)
        and (
            p_busqueda is null or p_busqueda = '' or
            p.nombre like concat('%', p_busqueda, '%') or
            p.apellido like concat('%', p_busqueda, '%') or
            p.documento_identificacion like concat('%', p_busqueda, '%') or
            a.tipo_actividad like concat('%', p_busqueda, '%')
        )
    order by a.fecha_actividad desc, a.hora_inicio desc;
end//

-- actualizar una actividad
create procedure actualizar_actividad(
    in p_id_actividad int,
    in p_id_paciente int,
    in p_tipo_actividad varchar(100),
    in p_descripcion_actividad text,
    in p_fecha_actividad date,
    in p_hora_inicio time,
    in p_hora_fin time
)
begin
    update tb_actividad set
        id_paciente = p_id_paciente,
        tipo_actividad = p_tipo_actividad,
        descripcion_actividad = p_descripcion_actividad,
        fecha_actividad = p_fecha_actividad,
        hora_inicio = p_hora_inicio,
        hora_fin = p_hora_fin
    where id_actividad = p_id_actividad;
end//

-- eliminar una actividad
create procedure eliminar_actividad(
    in p_id_actividad int
)
begin
    update tb_actividad
    set estado_actividad = 'inactivo'
    where id_actividad = p_id_actividad;
end//

-- consultar actividades (cuidador)
CREATE PROCEDURE `consultar_actividades_cuidador`(
    IN p_id_cuidador INT,
    IN p_busqueda VARCHAR(100),
    IN p_estado_filtro VARCHAR(20)
)
BEGIN
    SELECT
        a.id_actividad, 
        a.tipo_actividad, 
        a.descripcion_actividad,
        a.fecha_actividad, 
        -- AÑADIMOS LAS HORAS FORMATEADAS PARA QUE SE VEAN BIEN --
        TIME_FORMAT(a.hora_inicio, '%h:%i %p') AS hora_inicio,
        TIME_FORMAT(a.hora_fin, '%h:%i %p') AS hora_fin,
        a.estado_actividad,
        CONCAT(p.nombre, ' ', p.apellido) AS nombre_paciente
    FROM tb_actividad a
    JOIN tb_paciente p ON a.id_paciente = p.id_paciente
    JOIN tb_paciente_asignado pa ON p.id_paciente = pa.id_paciente
    WHERE
        pa.id_usuario_cuidador = p_id_cuidador AND pa.estado = 'activo'
        AND a.estado_actividad != 'inactivo'
        AND (p_estado_filtro IS NULL OR p_estado_filtro = '' OR a.estado_actividad = p_estado_filtro)
        AND (
            (p_busqueda IS NULL OR p_busqueda = '') OR
            (p.nombre LIKE CONCAT('%', p_busqueda, '%')) OR
            (p.apellido LIKE CONCAT('%', p_busqueda, '%')) OR
            (p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')) OR
            (a.tipo_actividad LIKE CONCAT('%', p_busqueda, '%'))
        )
    ORDER BY a.fecha_actividad DESC;
END//


create procedure completar_actividad(
    in p_id_actividad int,
    in p_id_cuidador int
)
begin
    /*  mirar que la actividad este asignada al cuidador que hace la quiere completar*/
    if exists (
        select 1
        from tb_actividad a
        join tb_paciente_asignado pa on a.id_paciente = pa.id_paciente
        where a.id_actividad = p_id_actividad
        and pa.id_usuario_cuidador = p_id_cuidador
        and a.estado_actividad = 'pendiente'
    )
    then
        update tb_actividad
        set estado_actividad = 'completada'
        where id_actividad = p_id_actividad;
    else
        signal sqlstate '45000' set message_text = 'Error: La actividad no existe, ya fue completada o no tiene permiso para modificarla.';
    end if;
end//
delimiter ;
