use gericare_connect;

delimiter //
create procedure actualizar_usuario(
    in p_id_usuario int,
    in p_tipo_documento enum('cc','ce','pa'),
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

    -- validaciones
    if exists (select 1 from tb_usuario where documento_identificacion = p_documento_identificacion and id_usuario != p_id_usuario) then
        signal sqlstate '45000' set message_text = 'error: el documento ya está registrado por otro usuario.';
    end if;
    if exists (select 1 from tb_usuario where correo_electronico = p_correo_electronico and id_usuario != p_id_usuario) then
        signal sqlstate '45000' set message_text = 'error: el correo ya está registrado por otro usuario.';
    end if;
    select id_rol into v_id_rol from tb_rol where lower(nombre_rol) = lower(trim(p_nombre_rol));
    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'error: el rol proporcionado no es válido.';
    end if;

    -- lógica de limpieza y validación
    if lower(p_nombre_rol) = 'familiar' then
        set p_fecha_contratacion = null;
        set p_tipo_contrato = null;
        set p_contacto_emergencia = null;
        set p_fecha_nacimiento = null;
        if p_parentesco is null or trim(p_parentesco) = '' then
             signal sqlstate '45000' set message_text = 'error: un familiar debe tener un parentesco.';
        end if;
    else
        set p_parentesco = null;
        if p_fecha_contratacion is null or p_tipo_contrato is null or p_contacto_emergencia is null or p_fecha_nacimiento is null then
            signal sqlstate '45000' set message_text = 'error: los datos de empleado son obligatorios.';
        end if;
    end if;

    start transaction;

    update tb_usuario set
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

    if p_numero_telefono is not null and p_numero_telefono != '' then
        if (select count(*) from tb_telefono where id_usuario = p_id_usuario and estado = 'activo') > 0 then
            update tb_telefono set numero_telefono = p_numero_telefono where id_usuario = p_id_usuario and estado = 'activo' limit 1;
        else
            insert into tb_telefono (id_usuario, numero_telefono, estado) values (p_id_usuario, p_numero_telefono, 'activo');
        end if;
    end if;

    commit;
end//
delimiter ;

/* Registrar */
use gericare_connect;

delimiter //
/* empleados */
create procedure registrar_empleado(
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña_hash varchar(255),
    in p_numero_telefono varchar(20),
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_fecha_nacimiento date,
    in p_nombre_rol varchar(50)
)
begin
    declare v_id_usuario int;
    declare v_id_rol int;

    if exists (select 1 from tb_usuario where documento_identificacion = p_documento_identificacion) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese número de documento.';
    end if;

    if exists (select 1 from tb_usuario where correo_electronico = p_correo_electronico) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese correo electrónico.';
    end if;

    select id_rol into v_id_rol from tb_rol where nombre_rol = p_nombre_rol;
    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'Error: El rol especificado no es válido.';
    end if;

    start transaction;

    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido, direccion,
        correo_electronico, contraseña, -- <-- CORRECCIÓN APLICADA AQUÍ
        fecha_contratacion, tipo_contrato, contacto_emergencia, fecha_nacimiento,
        id_rol, parentesco
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido, p_direccion,
        p_correo_electronico, p_contraseña_hash,
        p_fecha_contratacion, p_tipo_contrato, p_contacto_emergencia, p_fecha_nacimiento,
        v_id_rol, null
    );

    set v_id_usuario = last_insert_id();

    if p_numero_telefono is not null and p_numero_telefono != '' then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (v_id_usuario, p_numero_telefono);
    end if;

    commit;

    select v_id_usuario as id_usuario_creado;
end //

/* familiar */
delimiter //
create procedure registrar_familiar(
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña_hash varchar(255),
    in p_numero_telefono varchar(20),
    in p_parentesco varchar(50)
)
begin
    declare v_id_usuario int;
    declare v_id_rol int;

    -- Validaciones
    if exists (select 1 from tb_usuario where documento_identificacion = p_documento_identificacion) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese número de documento.';
    end if;
    if exists (select 1 from tb_usuario where correo_electronico = p_correo_electronico) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese correo electrónico.';
    end if;

    -- Obtener rol 'Familiar'
    select id_rol into v_id_rol from tb_rol where nombre_rol = 'Familiar';
    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'Error: El rol "Familiar" no se encuentra en la base de datos.';
    end if;

    start transaction;

    -- Solo se insertan los campos relevantes para un familiar
    -- La bd se encarga de poner NULL en los demás campos por defecto
    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido, direccion,
        correo_electronico, contraseña, parentesco, id_rol
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido, p_direccion,
        p_correo_electronico, p_contraseña_hash, p_parentesco, v_id_rol
    );

    set v_id_usuario = last_insert_id();

    -- Inserción de teléfono
    if p_numero_telefono is not null and p_numero_telefono != '' then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (v_id_usuario, p_numero_telefono);
    end if;

    commit;
    select v_id_usuario as id_usuario_creado;
end //
delimiter ;