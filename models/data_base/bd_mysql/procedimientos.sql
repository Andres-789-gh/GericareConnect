use gericare_connect;

delimiter //
create procedure insertarusuariogenerico(
    in p_tipo_documento enum ('CC', 'CE', 'PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña_hash varchar(255),
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_parentesco varchar(50),
    in p_nombre_rol varchar(50)
)
begin
    declare v_nuevo_id_usuario int;
    declare v_id_rol int;

    select id_rol into v_id_rol from tb_rol where nombre_rol = p_nombre_rol;

    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'Error: El rol especificado no existe.';
    end if;

    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido, fecha_nacimiento,
        direccion, correo_electronico, contraseña_hash, fecha_contratacion, tipo_contrato,
        contacto_emergencia, parentesco, estado
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento,
        p_direccion, p_correo_electronico, p_contraseña_hash, p_fecha_contratacion, p_tipo_contrato,
        p_contacto_emergencia, p_parentesco, 'Activo'
    );

    set v_nuevo_id_usuario = last_insert_id();

    insert into tb_usuario_rol (id_usuario, id_rol) values (v_nuevo_id_usuario, v_id_rol);
end //

create procedure actualizarusuariogenerico(
    in p_id_usuario int,
    in p_tipo_documento enum ('CC', 'CE', 'PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_estado enum('Activo', 'Inactivo'),
    in p_contraseña_hash varchar(255),
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_parentesco varchar(50)
)
begin
    update tb_usuario set
        tipo_documento = p_tipo_documento,
        documento_identificacion = p_documento_identificacion,
        nombre = p_nombre,
        apellido = p_apellido,
        fecha_nacimiento = p_fecha_nacimiento,
        direccion = p_direccion,
        correo_electronico = p_correo_electronico,
        estado = p_estado,
        contraseña_hash = p_contraseña_hash,
        fecha_contratacion = p_fecha_contratacion,
        tipo_contrato = p_tipo_contrato,
        contacto_emergencia = p_contacto_emergencia,
        parentesco = p_parentesco
    where id_usuario = p_id_usuario;
end //

create procedure desactivarusuario(in p_id_usuario int)
begin
    update tb_usuario set estado = 'Inactivo' where id_usuario = p_id_usuario;
    update tb_usuario_rol set estado = 'Inactivo' where id_usuario = p_id_usuario;
    update tb_telefono set estado = 'Inactivo' where id_usuario = p_id_usuario;
end //

create procedure consultarusuario(
    in p_id_usuario int,
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50)
)
begin
    select u.*, group_concat(r.nombre_rol separator ', ') as roles_asignados
    from tb_usuario u
    left join tb_usuario_rol ur on u.id_usuario = ur.id_usuario and ur.estado = 'Activo'
    left join tb_rol r on ur.id_rol = r.id_rol
    where
        u.estado = 'Activo'
        and (p_id_usuario is null or u.id_usuario = p_id_usuario)
        and (p_documento_identificacion is null or u.documento_identificacion = p_documento_identificacion)
        and (p_nombre is null or u.nombre like concat('%', p_nombre, '%'))
        and (p_apellido is null or u.apellido like concat('%', p_apellido, '%'))
    group by u.id_usuario;
end //



create procedure insertarpaciente(
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_genero enum('Masculino','Femenino'),
    in p_contacto_emergencia varchar(20),
    in p_estado_civil varchar(30),
    in p_tipo_sangre enum('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    in p_seguro_medico varchar(100),
    in p_numero_seguro varchar(50),
    in p_id_usuario_familiar int
)
begin
    insert into tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia,
        estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado
    ) values (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia,
        p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo'
    );
end //

create procedure actualizarpaciente(
    in p_id_paciente int,
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_genero enum('Masculino','Femenino'),
    in p_contacto_emergencia varchar(20),
    in p_estado_civil varchar(30),
    in p_tipo_sangre enum('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    in p_seguro_medico varchar(100),
    in p_numero_seguro varchar(50),
    in p_id_usuario_familiar int,
    in p_estado enum('Activo', 'Inactivo')
)
begin
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
        id_usuario_familiar = p_id_usuario_familiar,
        estado = p_estado
    where id_paciente = p_id_paciente;
end //

create procedure desactivarpaciente(in p_id_paciente int)
begin
    update tb_paciente set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_historia_clinica set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_tratamiento set estado_tratamiento = 'Cancelado' where id_paciente = p_id_paciente and estado_tratamiento = 'Activo';
    update tb_actividad set estado_actividad = 'Cancelada' where id_paciente = p_id_paciente and estado_actividad = 'Pendiente';
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_solicitud set estado_solicitud = 'Cancelada' where id_paciente = p_id_paciente and estado_solicitud = 'Pendiente';
end //

create procedure consultarpaciente(
    in p_id_paciente int,
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50)
)
begin
    select p.*, u.nombre as nombre_familiar, u.apellido as apellido_familiar
    from tb_paciente p
    left join tb_usuario u on p.id_usuario_familiar = u.id_usuario and u.estado = 'Activo'
    where
        p.estado = 'Activo'
        and (p_id_paciente is null or p.id_paciente = p_id_paciente)
        and (p_documento_identificacion is null or p.documento_identificacion = p_documento_identificacion)
        and (p_nombre is null or p.nombre like concat('%', p_nombre, '%'))
        and (p_apellido is null or p.apellido like concat('%', p_apellido, '%'));
end //



create procedure insertarhistoriaclinica(
    in p_id_paciente int,
    in p_id_usuario_cuidador int,
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
        id_paciente, id_usuario_cuidador, estado_salud, condiciones,
        antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_estado_salud, p_condiciones,
        p_antecedentes_medicos, p_alergias, p_dietas_especiales, p_fecha_ultima_consulta, p_observaciones, 'Activo'
    );
end //

create procedure desactivarhistoriaclinica(in p_id_historia_clinica int)
begin
    update tb_historia_clinica set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
    update tb_historia_clinica_medicamento set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
    update tb_historia_clinica_enfermedad set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
end //

create procedure consultarhistoriaclinicaporpaciente(in p_id_paciente int)
begin
    select hc.*, p.nombre as nombre_paciente, p.apellido as apellido_paciente,
           u.nombre as nombre_cuidador, u.apellido as apellido_cuidador
    from tb_historia_clinica hc
    join tb_paciente p on hc.id_paciente = p.id_paciente
    left join tb_usuario u on hc.id_usuario_cuidador = u.id_usuario
    where hc.id_paciente = p_id_paciente and hc.estado = 'Activo';
end //



create procedure asignarpacienteacuidador(
    in p_id_usuario_cuidador int,
    in p_id_usuario_administrador int,
    in p_id_paciente int,
    in p_descripcion varchar(250)
)
begin
    insert into tb_paciente_asignado (
        id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion, estado
    ) values (
        p_id_usuario_cuidador, p_id_usuario_administrador, p_id_paciente, p_descripcion, 'Activo'
    );
end //

create procedure desactivarpacienteasignado(in p_id_paciente_asignado int)
begin
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente_asignado = p_id_paciente_asignado;
end //


create procedure insertaractividad(
    in p_id_paciente int,
    in p_id_usuario_cuidador int,
    in p_tipo_actividad varchar(100),
    in p_descripcion_actividad text,
    in p_fecha_actividad date,
    in p_hora_inicio time,
    in p_hora_fin time
)
begin
    insert into tb_actividad (
        id_paciente, id_usuario_cuidador, tipo_actividad, descripcion_actividad,
        fecha_actividad, hora_inicio, hora_fin, estado_actividad
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_tipo_actividad, p_descripcion_actividad,
        p_fecha_actividad, p_hora_inicio, p_hora_fin, 'Pendiente'
    );
end //

create procedure cancelaractividad(in p_id_actividad int)
begin
    update tb_actividad set estado_actividad = 'Cancelada' where id_actividad = p_id_actividad;
end //

create procedure insertartratamiento(
    in p_id_paciente int,
    in p_id_usuario_cuidador int,
    in p_id_usuario_administrador int,
    in p_descripcion text,
    in p_instrucciones_especiales text,
    in p_fecha_inicio date,
    in p_fecha_fin date
)
begin
    insert into tb_tratamiento (
        id_paciente, id_usuario_cuidador, id_usuario_administrador, descripcion,
        instrucciones_especiales, fecha_inicio, fecha_fin, estado_tratamiento
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_id_usuario_administrador, p_descripcion,
        p_instrucciones_especiales, p_fecha_inicio, p_fecha_fin, 'Activo'
    );
end //

create procedure cancelartratamiento(in p_id_tratamiento int)
begin
    update tb_tratamiento set estado_tratamiento = 'Cancelado' where id_tratamiento = p_id_tratamiento;
end //

delimiter ;
