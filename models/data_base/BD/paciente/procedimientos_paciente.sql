use gericare_connect;

drop procedure if exists registrar_paciente;
drop procedure if exists actualizar_paciente;
drop procedure if exists consultar_pacientes;

delimiter //

create procedure registrar_paciente(
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_genero enum('masculino','femenino'),
    in p_contacto_emergencia varchar(20),
    in p_estado_civil varchar(30),
    in p_tipo_sangre enum('a+','a-','b+','b-','ab+','ab-','o+','o-'),
    in p_seguro_medico varchar(100),
    in p_numero_seguro varchar(50),
    in p_id_usuario_familiar int,
    in p_id_usuario_cuidador int,
    in p_id_usuario_administrador int,
    in p_descripcion_asignacion varchar(250)
)
begin
    declare v_id_paciente_creado int;
    start transaction;

    insert into tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero,
        contacto_emergencia, estado_civil, tipo_sangre, seguro_medico,
        numero_seguro, id_usuario_familiar, estado
    ) values (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero,
        p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico,
        p_numero_seguro, p_id_usuario_familiar, 'activo'
    );

    set v_id_paciente_creado = last_insert_id();

    if p_id_usuario_cuidador is not null then
        insert into tb_paciente_asignado(
            id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion
        ) values (
            p_id_usuario_cuidador, p_id_usuario_administrador, v_id_paciente_creado, p_descripcion_asignacion
        );
    end if;

    commit;
    select v_id_paciente_creado as id_paciente;
end//

create procedure actualizar_paciente(
    in p_id_paciente int,
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_genero enum('masculino','femenino'),
    in p_contacto_emergencia varchar(20),
    in p_estado_civil varchar(30),
    in p_tipo_sangre enum('a+','a-','b+','b-','ab+','ab-','o+','o-'),
    in p_seguro_medico varchar(100),
    in p_numero_seguro varchar(50),
    in p_id_usuario_familiar int,
    in p_id_usuario_cuidador int,
    in p_id_usuario_administrador int,
    in p_descripcion_asignacion varchar(250)
)
begin
    start transaction;

    update tb_paciente set
        documento_identificacion = p_documento_identificacion,
        nombre = p_nombre,
        apellido = p_apellido,
        fecha_nacimiento = p_fecha_nacimiento,
        genero = p_genero,
        contacto_emergencia = p_contacto_emergencia,
        estado_civil = p_estado_civil,
        tipo_sangre = p_tipo_sangre,
        seguro_medico = p_seguro_medico,
        numero_seguro = p_numero_seguro,
        id_usuario_familiar = p_id_usuario_familiar
    where id_paciente = p_id_paciente;

    update tb_paciente_asignado set estado = 'inactivo' where id_paciente = p_id_paciente;

    if p_id_usuario_cuidador is not null then
        insert into tb_paciente_asignado(
            id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion, estado
        ) values (
            p_id_usuario_cuidador, p_id_usuario_administrador, p_id_paciente, p_descripcion_asignacion, 'activo'
        );
    end if;

    commit;
end//

create procedure consultar_pacientes()
begin
    select *,
        timestampdiff(year, fecha_nacimiento, curdate()) as edad
    from tb_paciente
    where estado = 'activo'
    order by apellido, nombre;
end//

delimiter ;
