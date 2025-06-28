delimiter //

create procedure registrar_usuario(
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña varchar(255),
    in p_numero_telefono varchar(20),
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_fecha_nacimiento date,
    in p_parentesco varchar(50),
    in p_nombre_rol varchar(50)
)
begin
    declare v_id_usuario int;
    declare v_id_rol int;
    declare v_error_msg varchar(255);

    -- Validar duplicado por documento
    if exists (select 1 from tb_usuario where documento_identificacion = p_documento_identificacion) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese número de documento.';
    end if;

    -- Validar duplicado por correo
    if exists (select 1 from tb_usuario where correo_electronico = p_correo_electronico) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese correo electrónico.';
    end if;

    -- Obtener id del rol
    select id_rol into v_id_rol from tb_rol where nombre_rol = trim(p_nombre_rol);
    
    if v_id_rol is null then
        set v_error_msg = concat('Error: El rol "', trim(p_nombre_rol), '" no es válido.');
        signal sqlstate '45000' set message_text = v_error_msg;
    end if;

    -- Validación de datos según rol
    if p_nombre_rol = 'Familiar' then
        if p_fecha_contratacion is not null or p_tipo_contrato is not null or p_contacto_emergencia is not null or p_fecha_nacimiento is not null then
            signal sqlstate '45000' set message_text = 'Error: Un Familiar no debe tener datos de Cuidador o Administrador.';
        end if;
    else
        if p_fecha_contratacion is null or p_tipo_contrato is null or p_contacto_emergencia is null or p_fecha_nacimiento is null then
            signal sqlstate '45000' set message_text = 'Error: Datos de empleado incompletos.';
        end if;
    end if;

    start transaction;

    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido,
        direccion, correo_electronico, contraseña,
        fecha_contratacion, tipo_contrato, contacto_emergencia,
        fecha_nacimiento, parentesco, id_rol
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido,
        p_direccion, p_correo_electronico, p_contraseña,
        p_fecha_contratacion, p_tipo_contrato, p_contacto_emergencia,
        p_fecha_nacimiento, p_parentesco, v_id_rol
    );

    set v_id_usuario = last_insert_id();

    if p_numero_telefono is not null then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (v_id_usuario, p_numero_telefono);
    end if;

    commit;

    select v_id_usuario as id_usuario_creado;
end //

delimiter ;


delimiter //

create procedure actualizar_usuario(
    in p_id_usuario int,
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_numero_telefono varchar(20),
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_fecha_nacimiento date,
    in p_parentesco varchar(50),
    in p_nombre_rol varchar(50)
)
begin
    declare v_id_rol int;
    declare v_error_msg varchar(255);

    -- Validar documento duplicado (otro usuario)
    if exists (
        select 1 from tb_usuario
        where documento_identificacion = p_documento_identificacion
        and id_usuario != p_id_usuario
    ) then
        signal sqlstate '45000' set message_text = 'Error: El documento ya está registrado por otro usuario.';
    end if;

    -- Validar correo duplicado (otro usuario)
    if exists (
        select 1 from tb_usuario
        where correo_electronico = p_correo_electronico
        and id_usuario != p_id_usuario
    ) then
        signal sqlstate '45000' set message_text = 'Error: El correo ya está registrado por otro usuario.';
    end if;

    -- Obtener ID del rol
    select id_rol into v_id_rol from tb_rol where nombre_rol = trim(p_nombre_rol);
    
    if v_id_rol is null then
        set v_error_msg = concat('Error: El rol "', trim(p_nombre_rol), '" no es válido.');
        signal sqlstate '45000' set message_text = v_error_msg;
    end if;

    -- Validar atributos por rol
    if p_nombre_rol = 'Familiar' then
        if p_fecha_contratacion is not null or p_tipo_contrato is not null or p_contacto_emergencia is not null or p_fecha_nacimiento is not null then
            signal sqlstate '45000' set message_text = 'Error: Un Familiar no debe tener datos de Cuidador o Administrador.';
        end if;
    else
        if p_fecha_contratacion is null or p_tipo_contrato is null or p_contacto_emergencia is null or p_fecha_nacimiento is null then
            signal sqlstate '45000' set message_text = 'Error: Datos de empleado incompletos.';
        end if;
    end if;

    start transaction;

    update tb_usuario
    set
        tipo_documento = p_tipo_documento,
        documento_identificacion = p_documento_identificacion,
        nombre = p_nombre,
        apellido = p_apellido,
        direccion = p_direccion,
        correo_electronico = p_correo_electronico,
        fecha_contratacion = p_fecha_contratacion,
        tipo_contrato = p_tipo_contrato,
        contacto_emergencia = p_contacto_emergencia,
        fecha_nacimiento = p_fecha_nacimiento,
        parentesco = p_parentesco,
        id_rol = v_id_rol
    where id_usuario = p_id_usuario;

    -- Actualizar/insertar teléfono
    if p_numero_telefono is not null then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (p_id_usuario, p_numero_telefono)
        on duplicate key update numero_telefono = p_numero_telefono;
    end if;

    commit;
end //

delimiter ;


delimiter //

create procedure registrar_familiar(
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña varchar(255),
    in p_numero_telefono varchar(20),
    in p_parentesco varchar(50)
)
begin
    declare v_id_usuario int;
    declare v_id_rol int;

    -- Obtener id del rol 'Familiar'
    select id_rol into v_id_rol from tb_rol where nombre_rol = 'Familiar';

    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'Error: El rol "Familiar" no está registrado.';
    end if;

    -- Validar documento duplicado
    if exists (
        select 1 from tb_usuario where documento_identificacion = p_documento_identificacion
    ) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese número de documento.';
    end if;

    -- Validar correo duplicado
    if exists (
        select 1 from tb_usuario where correo_electronico = p_correo_electronico
    ) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese correo electrónico.';
    end if;

    start transaction;

    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido,
        direccion, correo_electronico, contraseña,
        parentesco, id_rol
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido,
        p_direccion, p_correo_electronico, p_contraseña,
        p_parentesco, v_id_rol
    );

    set v_id_usuario = last_insert_id();

    if p_numero_telefono is not null then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (v_id_usuario, p_numero_telefono);
    end if;

    commit;

    select v_id_usuario as id_usuario_creado;
end //

delimiter ;
