delimiter //
-- inserta un nuevo registro de entrada o salida.
create procedure registrar_entrada_salida(
    in p_id_usuario_cuidador int,
    in p_id_paciente int,
    in p_tipo_movimiento enum('Entrada', 'Salida'),
    in p_motivo varchar(250),
    in p_observaciones text
)
begin
    insert into tb_entrada_salida_paciente (
        id_usuario_cuidador, 
        id_paciente, 
        fecha_entrada_salida_paciente,
        tipo_movimiento, 
        motivo_entrada_salida_paciente, 
        observaciones,
        id_usuario_administrador -- este campo se deja NULL por defecto en la inserción
    ) values (
        p_id_usuario_cuidador, 
        p_id_paciente, 
        now(),
        p_tipo_movimiento, 
        p_motivo, 
        p_observaciones,
        null -- el valor queda NULL por que el admin no registra
    );
    -- Devuelve el ID del registro recién creado para confirmación.
    select last_insert_id() as id_registro_creado;
end//
delimiter ;

delimiter //
-- consulta el historial si no se da un id de paciente los trae todos
create procedure consultar_historial_paciente(
    in p_id_paciente int
)
begin
    select 
        esp.id_entrada_salida_paciente,
        esp.fecha_entrada_salida_paciente,
        esp.tipo_movimiento,
        esp.motivo_entrada_salida_paciente,
        esp.observaciones,
        concat(p.nombre, ' ', p.apellido) as nombre_paciente,
        concat(u_cuidador.nombre, ' ', u_cuidador.apellido) as nombre_cuidador,
        concat(u_admin.nombre, ' ', u_admin.apellido) as nombre_administrador
    from 
        tb_entrada_salida_paciente esp
    join 
        tb_paciente p on esp.id_paciente = p.id_paciente
    join 
        tb_usuario u_cuidador on esp.id_usuario_cuidador = u_cuidador.id_usuario
    left join 
        tb_usuario u_admin on esp.id_usuario_administrador = u_admin.id_usuario
    where 
        (p_id_paciente is null or esp.id_paciente = p_id_paciente)
    order by 
        esp.fecha_entrada_salida_paciente desc;
end//
delimiter ;

delimiter //
-- actualiza las observaciones de un registro de salida/ingreso
create procedure actualizar_observaciones_registro(
    in p_id_registro int,
    in p_nuevas_observaciones text
)
begin
    update tb_entrada_salida_paciente set
        observaciones = p_nuevas_observaciones
    where 
        id_entrada_salida_paciente = p_id_registro;
end//
delimiter ;
/*
drop  procedure actualizar_observaciones_registro;
drop  procedure registrar_entrada_salida;
drop  procedure consultar_historial_paciente;
*/