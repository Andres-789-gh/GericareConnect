use gericare_connect;

delimiter //

-- procedimiento 1: registrar un usuario

-- inserta un usuario, su teléfono y sus roles.
-- validar que un usuario no pueda ser 'administrador' y 'cuidador' simultáneamente.
-- utilizar transacciones para garantizar la integridad de los datos.

create procedure registrar_usuario(
    -- parámetros de la tabla tb_usuario
    in p_tipo_documento enum('CC','CE','PA'),
    in p_documento_identificacion int,
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),
    in p_contraseña varchar(255),
    
    -- parámetros para la tabla tb_telefono
    in p_numero_telefono varchar(20),
    
    -- parámetros opcionales para roles específicos
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_parentesco varchar(50),

    -- parámetros para roles (ej: 'Cuidador,Familiar' o 'Administrador')
    in p_roles varchar(255)
)
begin
    declare v_id_usuario int;
    declare v_id_rol int;
    declare v_rol_actual varchar(50);
    declare v_roles_restantes varchar(255);
    declare v_pos int;
    declare v_error_msg varchar(255); -- variable añadida para el mensaje de error
    
    -- validación duplicado documento
    if exists (select 1 from tb_usuario where documento_identificacion = p_documento_identificacion) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese número de documento.';
    end if;

    -- validación duplicado correo
    if exists (select 1 from tb_usuario where correo_electronico = p_correo_electronico) then
        signal sqlstate '45000' set message_text = 'Error: Ya existe un usuario con ese correo electrónico.';
    end if;

    -- validación de roles: no se puede ser Administrador y Cuidador a la vez.
    if find_in_set('Administrador', p_roles) > 0 and find_in_set('Cuidador', p_roles) > 0 then
        signal sqlstate '45000' set message_text = 'Error: Un usuario no puede ser "Administrador" y "Cuidador" a la vez.';
    end if;

    -- iniciar transacción para asegurar que todas las inserciones se completen
    start transaction;

    -- 1. insertar en tb_usuario
    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido, fecha_nacimiento,
        direccion, correo_electronico, contraseña,
        fecha_contratacion, tipo_contrato, contacto_emergencia, parentesco
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento,
        p_direccion, p_correo_electronico, p_contraseña, -- en una aplicación real, la contraseña debería ser un hash
        p_fecha_contratacion, p_tipo_contrato, p_contacto_emergencia, p_parentesco
    );

    set v_id_usuario = last_insert_id();

    -- 2. insertar en tb_telefono
    if p_numero_telefono is not null and v_id_usuario is not null then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (v_id_usuario, p_numero_telefono);
    end if;

    -- 3. insertar roles en tb_usuario_rol
    set v_roles_restantes = p_roles;
    while length(v_roles_restantes) > 0 and v_id_usuario is not null do
        set v_pos = instr(v_roles_restantes, ',');
        if v_pos = 0 then
            set v_rol_actual = v_roles_restantes;
            set v_roles_restantes = '';
        else
            set v_rol_actual = substring(v_roles_restantes, 1, v_pos - 1);
            set v_roles_restantes = substring(v_roles_restantes, v_pos + 1);
        end if;

        -- obtener el id del rol
        select id_rol into v_id_rol from tb_rol where nombre_rol = trim(v_rol_actual);

        -- si el rol existe, insertarlo
        if v_id_rol is not null then
            insert into tb_usuario_rol (id_usuario, id_rol) values (v_id_usuario, v_id_rol);
        else
            -- si un rol no existe, se revierte toda la transacción
            rollback;
            set v_error_msg = concat('Error: El rol "', trim(v_rol_actual), '" no es válido.');
            signal sqlstate '45000' set message_text = v_error_msg;
        end if;

    end while;

    commit;
    select v_id_usuario as 'id_usuario_creado';

end //


-- -----------------------------------------------------------------------------
-- procedimiento 2: actualizar un usuario

-- actualizar los datos de un usuario en tb_usuario y su teléfono en tb_telefono.
-- sincronizar los roles del usuario: desactivar los que ya no tiene y activar/insertar los nuevos.
-- validar que la nueva combinación de roles no incluya 'Administrador' y 'Cuidador' a la vez.
-- delimiter //
create procedure actualizar_usuario(
    -- parámetro de búsqueda
    in p_id_usuario int,
	in p_tipo_documento varchar(50),
    in p_documento_identificacion int,

    -- parámetros de la tabla tb_usuario a actualizar
    in p_nombre varchar(50),
    in p_apellido varchar(50),
    in p_fecha_nacimiento date,
    in p_direccion varchar(250),
    in p_correo_electronico varchar(100),

    -- parámetro para la tabla tb_telefono a actualizar
    in p_numero_telefono varchar(20),

    -- parámetros opcionales a actualizar
    in p_fecha_contratacion date,
    in p_tipo_contrato varchar(50),
    in p_contacto_emergencia varchar(20),
    in p_parentesco varchar(50),

    -- la nueva lista completa de roles para el usuario (ej: 'Familiar' o 'Cuidador,Familiar')
    in p_roles varchar(255)
)
begin
    declare v_id_rol int;
    declare v_rol_actual varchar(50);
    declare v_roles_restantes varchar(255);
    declare v_pos int;
    declare v_id_usuario_rol_existente int;
    declare v_error_msg varchar(255); -- variable añadida para el mensaje de error
    
    -- validación de documentos duplicados
	if exists (
        select 1 from tb_usuario 
        where documento_identificacion = (
            select documento_identificacion from tb_usuario where id_usuario = p_id_usuario
        )
        and id_usuario != p_id_usuario
    ) then
        signal sqlstate '45000' set message_text = 'El documento ya está registrado por otro usuario.';
    end if;

	-- validación de correos duplicados
    if exists (
        select 1 from tb_usuario 
        where correo_electronico = p_correo_electronico
        and id_usuario != p_id_usuario
    ) then
        signal sqlstate '45000' set message_text = 'El correo ya está registrado por otro usuario.';
    end if;

    -- validación de la nueva combinación de roles
    if find_in_set('Administrador', p_roles) > 0 and find_in_set('Cuidador', p_roles) > 0 then
        signal sqlstate '45000' set message_text = 'Error: Un usuario no puede ser "Administrador" y "Cuidador" al mismo tiempo.';
    end if;

    start transaction;

    -- 1. actualizar tb_usuario
    update tb_usuario
    set
        nombre = p_nombre,
        apellido = p_apellido,
        fecha_nacimiento = p_fecha_nacimiento,
        direccion = p_direccion,
        correo_electronico = p_correo_electronico,
        
        fecha_contratacion = p_fecha_contratacion,
        tipo_contrato = p_tipo_contrato,
        contacto_emergencia = p_contacto_emergencia,
        parentesco = p_parentesco
    where id_usuario = p_id_usuario;

    -- 2. actualizar tb_telefono (upsert)
    if p_numero_telefono is not null then
        insert into tb_telefono (id_usuario, numero_telefono)
        values (p_id_usuario, p_numero_telefono)
        on duplicate key update numero_telefono = p_numero_telefono;
    end if;

    -- 3. sincronizar roles
    -- se desactivan todos los roles actuales del usuario.
    update tb_usuario_rol set estado = 'Inactivo' where id_usuario = p_id_usuario;

    -- activar o insertar los nuevos roles de la lista p_roles.
    set v_roles_restantes = p_roles;
    while length(v_roles_restantes) > 0 do
        set v_pos = instr(v_roles_restantes, ',');
        if v_pos = 0 then
            set v_rol_actual = v_roles_restantes;
            set v_roles_restantes = '';
        else
            set v_rol_actual = substring(v_roles_restantes, 1, v_pos - 1);
            set v_roles_restantes = substring(v_roles_restantes, v_pos + 1);
        end if;

        -- obtener el id del rol
        select id_rol into v_id_rol from tb_rol where nombre_rol = trim(v_rol_actual);

        if v_id_rol is not null then
            -- verificar si la asignación de rol ya existe para este usuario
            select id_usuario_rol into v_id_usuario_rol_existente 
            from tb_usuario_rol 
            where id_usuario = p_id_usuario and id_rol = v_id_rol;

            if v_id_usuario_rol_existente is not null then
                -- si ya existe, simplemente lo reactivamos
                update tb_usuario_rol set estado = 'Activo' where id_usuario_rol = v_id_usuario_rol_existente;
            else
                -- si no existe, lo insertamos como activo
                insert into tb_usuario_rol (id_usuario, id_rol, estado) values (p_id_usuario, v_id_rol, 'Activo');
            end if;
        else
            rollback;
            set v_error_msg = concat('Error: El rol "', trim(v_rol_actual), '" no es válido.');
            signal sqlstate '45000' set message_text = v_error_msg;
        end if;
        
    end while;

    commit;

end //

delimiter ;