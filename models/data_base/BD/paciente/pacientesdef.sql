use gericare_connect;

-- borrar de procedimientos
drop procedure if exists registrar_paciente;
drop procedure if exists actualizar_paciente;
drop procedure if exists consultar_pacientes;

delimiter //
-- procedimientos crud de pacientes

-- crear un paciente
create procedure registrar_paciente(
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
    insert into tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado)
    values (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo');
end//

-- actualizar un paciente
create procedure actualizar_paciente(
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
    in p_id_usuario_familiar int
)
begin
    update tb_paciente set
        documento_identificacion = p_documento_identificacion, nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, genero = p_genero, contacto_emergencia = p_contacto_emergencia, estado_civil = p_estado_civil, tipo_sangre = p_tipo_sangre, seguro_medico = p_seguro_medico, numero_seguro = p_numero_seguro, id_usuario_familiar = p_id_usuario_familiar
    where id_paciente = p_id_paciente;
end//

-- consultar los pacientes para mostrarlos en la tabla del administrador.
create procedure consultar_pacientes()
begin
    -- se calcula la edad directamente en la consulta
    select *, timestampdiff(year, fecha_nacimiento, curdate()) as edad
    from tb_paciente
    where estado = 'Activo' order by apellido, nombre;
end//
delimiter ;