create database gericare_connect;
use gericare_connect;

-- tabla rol
create table tb_rol (
    id_rol int primary key auto_increment,
    nombre_rol enum('Familiar','Cuidador','Administrador') not null,
    descripcion_rol varchar(250) null
);

-- tabla usuario
create table tb_usuario (
    id_usuario int primary key auto_increment,
    tipo_documento enum('CC','CE','PA') not null,
    documento_identificacion int not null unique,
    nombre varchar(50) not null,
    apellido varchar(50) not null,
    direccion varchar(250) not null,
    correo_electronico varchar(100) not null unique,
    contraseña varchar(255) not null,
    estado enum('Activo','Inactivo') default 'Activo',
    id_rol int not null,
    -- atributos administrador/cuidador
    fecha_contratacion date null,
    tipo_contrato varchar(50) null,
    contacto_emergencia varchar(20) null,
    fecha_nacimiento date null,
    -- atributo familiar
    parentesco varchar(50) null
);

-- tabla paciente
create table tb_paciente (
    id_paciente int primary key auto_increment,
    documento_identificacion int not null unique,
    nombre varchar(50) not null,
    apellido varchar(50) not null,
    fecha_nacimiento date not null,
    genero enum('Masculino','Femenino') not null,
    contacto_emergencia varchar(20) not null,
    estado_civil varchar(30) not null,
    tipo_sangre enum('A+','A-','B+','B-','AB+','AB-','O+','O-') not null,
    seguro_medico varchar(100) null,
    numero_seguro varchar(50) null,
    id_usuario_familiar int null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla entrada salida paciente
create table tb_entrada_salida_paciente (
    id_entrada_salida_paciente int primary key auto_increment,
    id_usuario_cuidador int not null,
    id_usuario_administrador int not null, -- El admin gestiona la entrada/salida del paciente
    id_paciente int not null,
    fecha_entrada_salida_paciente datetime not null,
    tipo_movimiento enum('Entrada','Salida') not null,
    motivo_entrada_salida_paciente varchar(250) not null,
    observaciones text null
);

-- tabla historia_clinica
create table tb_historia_clinica (
    id_historia_clinica int primary key auto_increment,
    id_paciente int not null,
    id_usuario_administrador int null,
    estado_salud text null,
    condiciones text null,
    antecedentes_medicos text null,
    alergias text null,
    dietas_especiales text null,
    fecha_ultima_consulta date null,
    observaciones text null,
    estado enum('Activo','Inactivo') default 'Activo'
);


-- tabla cirugia
create table tb_historia_clinica_cirugia (
    id_cirugia int primary key auto_increment,
    id_historia_clinica int not null,
    descripcion_cirugia varchar(250) not null,
    fecha_cirugia date null,
    observaciones text null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla intermedia historia_clinica_medicamento
create table tb_historia_clinica_medicamento (
    id_hc_medicamento int primary key auto_increment,
    id_historia_clinica int not null,
    id_medicamento int not null,
    dosis varchar(100) null,
    frecuencia varchar(100) null,
    instrucciones varchar(250) null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla medicamento
create table tb_medicamento (
    id_medicamento int primary key auto_increment,
    nombre_medicamento varchar(100) not null,
    descripcion_medicamento varchar(250) null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla intermedia historia_clinica_enfermedad
create table tb_historia_clinica_enfermedad (
    id_hc_enfermedad int primary key auto_increment,
    id_historia_clinica int not null,
    id_enfermedad int not null,
    fecha_diagnostico date null,
    observaciones text null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla enfermedad
create table tb_enfermedad (
    id_enfermedad int primary key auto_increment,
    nombre_enfermedad varchar(100) not null,
    descripcion_enfermedad varchar(250) null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla tratamiento
create table tb_tratamiento (
    id_tratamiento int primary key auto_increment,
    id_paciente int not null,
    id_usuario_administrador int not null,
    id_usuario_cuidador int not null,
    descripcion text not null,
    instrucciones_especiales text null,
    fecha_inicio date not null,
    fecha_fin date null,
    observaciones text null,
    estado_tratamiento enum('Pendiente','Completado','Inactivo') default 'Pendiente'
);

-- tabla actividad
create table tb_actividad (
    id_actividad int primary key auto_increment,
    id_paciente int not null,
    id_usuario_administrador int not null,
    tipo_actividad varchar(100) not null,
    descripcion_actividad text null,
    fecha_actividad date not null,
    hora_inicio time null,
    hora_fin time null,
    estado_actividad enum('Pendiente','Completada','Inactivo') default 'Pendiente'
);

-- tabla turno
create table tb_turno (
    id_turno int primary key auto_increment,
    id_usuario_cuidador int not null,
    dia_de_la_semana varchar(20) not null,
    hora_inicio time not null,
    hora_fin time not null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla solicitud
create table tb_solicitud (
    id_solicitud int primary key auto_increment,
    id_paciente int not null,
    id_usuario_familiar int not null,
    id_usuario_administrador int not null,
    tipo_solicitud enum('Salida','Registro','Retiro','Otro') not null,
    fecha_solicitud datetime not null default current_timestamp,
    urgencia_solicitud enum('Baja','Media','Alta','Urgente') not null,
    motivo_solicitud text not null,
    estado_solicitud enum('Pendiente','Aprobada','Rechazada','Inactivo') default 'Pendiente'
);

-- tabla paciente_asignado
create table tb_paciente_asignado (
    id_paciente_asignado int primary key auto_increment,
    id_usuario_cuidador int not null,
    id_usuario_administrador int not null,
    id_paciente int not null,
    descripcion varchar(250) null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- tabla telefono
create table tb_telefono (
    id_telefono int primary key auto_increment,
    id_usuario int not null,
    numero_telefono varchar(20) not null,
    estado enum('Activo','Inactivo') default 'Activo'
);

-- foreign keys
alter table tb_usuario
    add constraint fk_usuario_rol
    foreign key (id_rol) references tb_rol(id_rol);

alter table tb_paciente
    add constraint fk_paciente_usuario_familiar
    foreign key (id_usuario_familiar) references tb_usuario(id_usuario);

alter table tb_historia_clinica
    add constraint fk_historia_clinica_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_historia_clinica
    add constraint fk_historia_clinica_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_historia_clinica_medicamento
    add constraint fk_hc_medicamento_historia_clinica
    foreign key (id_historia_clinica) references tb_historia_clinica(id_historia_clinica);

alter table tb_historia_clinica_medicamento
    add constraint fk_hc_medicamento_medicamento
    foreign key (id_medicamento) references tb_medicamento(id_medicamento);

alter table tb_historia_clinica_enfermedad
    add constraint fk_hc_enfermedad_historia_clinica
    foreign key (id_historia_clinica) references tb_historia_clinica(id_historia_clinica);

alter table tb_historia_clinica_enfermedad
    add constraint fk_hc_enfermedad_enfermedad
    foreign key (id_enfermedad) references tb_enfermedad(id_enfermedad);

alter table tb_historia_clinica_cirugia
    add constraint fk_hc_cirugia_historia_clinica
    foreign key (id_historia_clinica) references tb_historia_clinica(id_historia_clinica);

alter table tb_tratamiento
    add constraint fk_tratamiento_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_tratamiento
    add constraint fk_tratamiento_usuario_cuidador
    foreign key (id_usuario_cuidador) references tb_usuario(id_usuario);

alter table tb_tratamiento
    add constraint fk_tratamiento_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_actividad
    add constraint fk_actividad_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_actividad
    add constraint fk_actividad_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_entrada_salida_paciente
    add constraint fk_entrada_salida_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_entrada_salida_paciente
    add constraint fk_entrada_salida_usuario_cuidador
    foreign key (id_usuario_cuidador) references tb_usuario(id_usuario);
    
alter table tb_entrada_salida_paciente
    add constraint fk_entrada_salida_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_turno
    add constraint fk_turno_usuario_cuidador
    foreign key (id_usuario_cuidador) references tb_usuario(id_usuario);

alter table tb_solicitud
    add constraint fk_solicitud_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_solicitud
    add constraint fk_solicitud_usuario_familiar
    foreign key (id_usuario_familiar) references tb_usuario(id_usuario);

alter table tb_solicitud
    add constraint fk_solicitud_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_paciente_asignado
    add constraint fk_paciente_asignado_usuario_cuidador
    foreign key (id_usuario_cuidador) references tb_usuario(id_usuario);

alter table tb_paciente_asignado
    add constraint fk_paciente_asignado_usuario_administrador
    foreign key (id_usuario_administrador) references tb_usuario(id_usuario);

alter table tb_paciente_asignado
    add constraint fk_paciente_asignado_paciente
    foreign key (id_paciente) references tb_paciente(id_paciente);

alter table tb_telefono
    add constraint fk_telefono_usuario
    foreign key (id_usuario) references tb_usuario(id_usuario);

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

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
        correo_electronico, contraseña,
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

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

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
            and u.estado = 'Activo'
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

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

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
    declare v_id_historia_clinica int;

    -- iniciar una transacción para asegurar que todas las operaciones se completen o ninguna lo haga
    start transaction;

    -- obtener el id de la historia clínica del paciente si existe
    select id_historia_clinica into v_id_historia_clinica
    from tb_historia_clinica
    where id_paciente = p_id_paciente_a_desactivar and estado = 'activo'
    limit 1;

    -- 1. desactivar el paciente
    update tb_paciente set estado = 'inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 2. desactivar todas las asignaciones del paciente a cuidadores
    update tb_paciente_asignado set estado = 'inactivo' where id_paciente = p_id_paciente_a_desactivar;

    -- 3. desactivar actividades del paciente
    update tb_actividad set estado_actividad = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_actividad = 'pendiente';
    
    -- 4. desactivar tratamientos del paciente
    update tb_tratamiento set estado_tratamiento = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_tratamiento = 'activo';

    -- 5. desactivar solicitudes relacionadas al paciente
    update tb_solicitud set estado_solicitud = 'inactivo'
    where id_paciente = p_id_paciente_a_desactivar and estado_solicitud = 'pendiente';

    -- 6. si el paciente tiene una historia clínica junto con sus detalles
    if v_id_historia_clinica is not null then
        -- desactivar la historia clínica principal
        update tb_historia_clinica set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar medicamentos asignados en esa historia
        update tb_historia_clinica_medicamento set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar enfermedades asignadas en esa historia
        update tb_historia_clinica_enfermedad set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;

        -- desactivar cirugías registradas en esa historia
        update tb_historia_clinica_cirugia set estado = 'inactivo' where id_historia_clinica = v_id_historia_clinica;
    end if;

    -- confirmar todos los cambios si no hubo errores
    commit;
end//
delimiter ;

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

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

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

-- usar la base de datos
use gericare_connect;

-- eliminar procedimientos
drop procedure if exists registrar_historia_clinica;
drop procedure if exists consultar_historia_clinica;
drop procedure if exists actualizar_historia_clinica;
drop procedure if exists eliminar_historia_clinica;
drop procedure if exists consultar_enfermedades_hc;
drop procedure if exists asignar_enfermedad_hc;
drop procedure if exists eliminar_enfermedad_hc;
drop procedure if exists consultar_medicamentos_hc;
drop procedure if exists asignar_medicamento_hc;
drop procedure if exists actualizar_medicamento_hc;
drop procedure if exists eliminar_medicamento_hc;
drop procedure if exists consultar_reporte_completo_hc;

delimiter $$

-- crud básico de historia clínica

create procedure registrar_historia_clinica(in p_id_paciente int, in p_id_usuario_administrador int, in p_estado_salud text, in p_condiciones text, in p_antecedentes_medicos text, in p_alergias text, in p_dietas_especiales text, in p_fecha_ultima_consulta date, in p_observaciones text)
begin
    insert into tb_historia_clinica (id_paciente, id_usuario_administrador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado)
    values (p_id_paciente, p_id_usuario_administrador, p_estado_salud, p_condiciones, p_antecedentes_medicos, p_alergias, p_dietas_especiales, p_fecha_ultima_consulta, p_observaciones, 'Activo');
end$$

-- cuenta las asignaciones para el "botón inteligente"
create procedure consultar_historia_clinica(in p_id_historia_clinica int, in p_busqueda varchar(100))
begin
    if p_id_historia_clinica is not null then
        select hc.*, concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo
        from tb_historia_clinica as hc
        join tb_paciente as p on hc.id_paciente = p.id_paciente
        where hc.id_historia_clinica = p_id_historia_clinica;
    else
        select
            hc.id_historia_clinica,
            concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo,
            date_format(hc.fecha_ultima_consulta, '%d/%m/%Y') as fecha_formateada,
            hc.estado_salud,
            (select count(*) from tb_historia_clinica_medicamento where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as med_count,
            (select count(*) from tb_historia_clinica_enfermedad where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as enf_count
        from tb_historia_clinica as hc
        join tb_paciente as p on hc.id_paciente = p.id_paciente
        where hc.estado = 'Activo'
            and (p_busqueda is null or p_busqueda = '' or
                 p.nombre like concat('%', p_busqueda, '%') or
                 p.apellido like concat('%', p_busqueda, '%') or
                 p.documento_identificacion like concat('%', p_busqueda, '%'))
        order by hc.id_historia_clinica desc;
    end if;
end$$

create procedure actualizar_historia_clinica(in p_id_historia_clinica int, in p_id_usuario_administrador int, in p_estado_salud text, in p_condiciones text, in p_antecedentes_medicos text, in p_alergias text, in p_dietas_especiales text, in p_fecha_ultima_consulta date, in p_observaciones text)
begin
    update tb_historia_clinica set id_usuario_administrador = p_id_usuario_administrador, estado_salud = p_estado_salud, condiciones = p_condiciones, antecedentes_medicos = p_antecedentes_medicos, alergias = p_alergias, dietas_especiales = p_dietas_especiales, fecha_ultima_consulta = p_fecha_ultima_consulta, observaciones = p_observaciones
    where id_historia_clinica = p_id_historia_clinica;
end$$

create procedure eliminar_historia_clinica(in p_id_historia_clinica int)
begin
    update tb_historia_clinica set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
end$$

-- gestión de asignaciones

create procedure consultar_enfermedades_hc(in p_id_historia_clinica int)
begin
    select hce.id_hc_enfermedad, e.id_enfermedad, e.nombre_enfermedad from tb_historia_clinica_enfermedad as hce join tb_enfermedad as e on hce.id_enfermedad = e.id_enfermedad where hce.id_historia_clinica = p_id_historia_clinica and hce.estado = 'Activo';
end$$

create procedure asignar_enfermedad_hc(in p_id_historia_clinica int, in p_id_enfermedad int)
begin
    if not exists (select 1 from tb_historia_clinica_enfermedad where id_historia_clinica = p_id_historia_clinica and id_enfermedad = p_id_enfermedad and estado = 'Activo') then
        insert into tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, fecha_diagnostico, estado) values (p_id_historia_clinica, p_id_enfermedad, curdate(), 'Activo');
        select last_insert_id() as id_asignacion;
    else
        select 0 as id_asignacion;
    end if;
end$$

create procedure eliminar_enfermedad_hc(in p_id_hc_enfermedad int)
begin
    delete from tb_historia_clinica_enfermedad where id_hc_enfermedad = p_id_hc_enfermedad;
end$$

create procedure consultar_medicamentos_hc(in p_id_historia_clinica int)
begin
    select hcm.id_hc_medicamento, m.id_medicamento, m.nombre_medicamento, hcm.dosis, hcm.frecuencia, hcm.instrucciones from tb_historia_clinica_medicamento as hcm join tb_medicamento as m on hcm.id_medicamento = m.id_medicamento where hcm.id_historia_clinica = p_id_historia_clinica and hcm.estado = 'Activo';
end$$

create procedure asignar_medicamento_hc(in p_id_historia_clinica int, in p_id_medicamento int, in p_dosis varchar(100), in p_frecuencia varchar(100), in p_instrucciones varchar(250))
begin
    insert into tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, dosis, frecuencia, instrucciones, estado) values (p_id_historia_clinica, p_id_medicamento, p_dosis, p_frecuencia, p_instrucciones, 'Activo');
    select last_insert_id() as id_asignacion;
end$$

create procedure actualizar_medicamento_hc(in p_id_hc_medicamento int, in p_dosis varchar(100), in p_frecuencia varchar(100), in p_instrucciones varchar(250))
begin
    update tb_historia_clinica_medicamento set dosis = p_dosis, frecuencia = p_frecuencia, instrucciones = p_instrucciones where id_hc_medicamento = p_id_hc_medicamento;
end$$

create procedure eliminar_medicamento_hc(in p_id_hc_medicamento int)
begin
    delete from tb_historia_clinica_medicamento where id_hc_medicamento = p_id_hc_medicamento;
end$$

-- procedimiento para el reporte completo
create procedure consultar_reporte_completo_hc(in p_id_historia_clinica int)
begin
    select
        -- datos de la historia
        hc.*,
        -- datos del paciente
        p.nombre as paciente_nombre,
        p.apellido as paciente_apellido,
        p.documento_identificacion as paciente_documento,
        p.fecha_nacimiento as paciente_fecha_nacimiento,
        -- datos del administrador que la gestionó
        u.nombre as admin_nombre,
        u.apellido as admin_apellido
    from
        tb_historia_clinica as hc
    join
        tb_paciente as p on hc.id_paciente = p.id_paciente
    left join
        tb_usuario as u on hc.id_usuario_administrador = u.id_usuario
    where
        hc.id_historia_clinica = p_id_historia_clinica;
end$$

delimiter ;

/*buscar desde el view de cuidador*/
delimiter $$

create procedure consultar_historias_cuidador(in p_id_cuidador int, in p_busqueda varchar(100))
begin
    select
        hc.id_historia_clinica,
        concat(p.nombre, ' ', p.apellido) as paciente_nombre_completo,
        date_format(hc.fecha_ultima_consulta, '%d/%m/%y') as fecha_formateada,
        hc.estado_salud,
        (select count(*) from tb_historia_clinica_medicamento where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as med_count,
        (select count(*) from tb_historia_clinica_enfermedad where id_historia_clinica = hc.id_historia_clinica and estado = 'Activo') as enf_count
    from
        tb_paciente_asignado pa
    join
        tb_historia_clinica hc on pa.id_paciente = hc.id_paciente
    join
        tb_paciente p on pa.id_paciente = p.id_paciente
    where
        pa.id_usuario_cuidador = p_id_cuidador
        and pa.estado = 'Activo'
        and hc.estado = 'Activo'
        and (p_busqueda is null or p_busqueda = '' or
             p.nombre like concat('%', p_busqueda, '%') or
             p.apellido like concat('%', p_busqueda, '%') or
             p.documento_identificacion like concat('%', p_busqueda, '%'));
end$$

delimiter ;

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */

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
create procedure consultar_actividades_cuidador(
    in p_id_cuidador int,
    in p_busqueda varchar(100),
    in p_estado_filtro varchar(20)
)
begin
    select
        a.id_actividad, 
        a.tipo_actividad, 
        a.descripcion_actividad,
        a.fecha_actividad, 
        time_format(a.hora_inicio, '%h:%i %p') as hora_inicio,
        time_format(a.hora_fin, '%h:%i %p') as hora_fin,
        a.estado_actividad,
        concat(p.nombre, ' ', p.apellido) as nombre_paciente
    from tb_actividad a
    join tb_paciente p on a.id_paciente = p.id_paciente
    join tb_paciente_asignado pa on p.id_paciente = pa.id_paciente
    where
        pa.id_usuario_cuidador = p_id_cuidador and pa.estado = 'activo'
        and a.estado_actividad != 'inactivo'
        and (p_estado_filtro is null or p_estado_filtro = '' or a.estado_actividad = p_estado_filtro)
        and (
            (p_busqueda is null or p_busqueda = '') or
            (p.nombre like concat('%', p_busqueda, '%')) or
            (p.apellido like concat('%', p_busqueda, '%')) or
            (p.documento_identificacion like concat('%', p_busqueda, '%')) or
            (a.tipo_actividad like concat('%', p_busqueda, '%'))
        )
    order by a.fecha_actividad desc;
end//

create procedure completar_actividad(
    in p_id_actividad int,
    in p_id_cuidador int
)
begin
    /* mirar que la actividad este asignada al cuidador que hace la quiere completar*/
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

/* ------------------------------------------------------------------ */
/* ------------------------------------------------------------------ */
/* pruebas */


insert into tb_rol (nombre_rol, descripcion_rol) values
('Administrador', 'Accede a todas las funciones del sistema'),
('Cuidador', 'Gestiona actividades, tratamientos e historias clínicas'),
('Familiar', 'Realiza solicitudes y ve la información de su paciente');

insert into tb_usuario (tipo_documento, documento_identificacion, nombre, apellido, direccion, correo_electronico, contraseña, estado, id_rol, fecha_contratacion, tipo_contrato, contacto_emergencia, fecha_nacimiento, parentesco) values
-- Administrador (contraseña: admin123)
('CC', 1001, 'Ana', 'Gómez', 'Calle 1 #23-45', 'ana.admin@gmail.com', '$2y$10$9.M.d9yI5b5G/i2C9n7yU.0s4kF9B2i.E2V2b8n1o3e7wJ6q4f7mG', 'Activo', 1, '2020-01-01', 'Término indefinido', '3123456789', '1980-05-10', null),

-- Cuidador (contraseña: cuidador123)
('CC', 1002, 'Luis', 'Pérez', 'Carrera 7 #89-12', 'luis.cuidador@gmail.com', '$2y$10$3pZ.W.0k7d3V3q.9s2z.U.o2uP4r7w8x1e7q5s4t3r2w1u0i9o8pL', 'Activo', 2, '2021-03-20', 'Por prestación', '3009876543', '1990-07-15', null),

-- Familiar (contraseña: familiar123)
('CC', 1003, 'Marta', 'Ramírez', 'Transversal 45 #67-89', 'marta.familiar@gmail.com', '$2y$10$1q2w3e4r5t6y7u8i9o0p.u.j5k6l7m8n9b0v1c2x3z4a5s6d7f8gH', 'Activo', 3, null, null, null, null, 'Madre');
 

-- 1. inserción de pacientes
insert into tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado) values 
(11223344, 'carlos', 'santana', '1948-07-20', 'masculino', '3105556677', 'soltero', 'a+', 'sura eps', '12345-a', 3, 'activo'),
(55667788, 'beatriz', 'pinzón', '1952-03-15', 'femenino', '3201112233', 'casada', 'o-', 'compensar eps', '67890-b', null, 'activo');

-- asignación de los pacientes al cuidador
insert into tb_paciente_asignado (id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion, estado) values
(2, 1, 1, 'monitoreo de presión arterial y asistencia en movilidad.', 'activo'),
(2, 1, 2, 'cuidado post-operatorio y administración de medicamentos.', 'activo');

-- 2. inserción de historias clínicas
-- medicamentos
insert into tb_medicamento (nombre_medicamento, descripcion_medicamento, estado) values
('paracetamol', 'analgésico y antipirético', 'activo'),
('losartán', 'medicamento para la presión arterial', 'activo'),
('insulina glargina', 'para el control de la diabetes', 'activo');

-- enfermedades
insert into tb_enfermedad (nombre_enfermedad, descripcion_enfermedad, estado) values
('diabetes mellitus tipo 2', 'enfermedad crónica que afecta la forma en que el cuerpo procesa el azúcar en la sangre', 'activo'),
('hipertensión arterial', 'condición crónica en la que la presión en las arterias es consistentemente alta', 'activo'),
('artrosis de rodilla', 'enfermedad degenerativa de las articulaciones', 'activo');

-- HC
insert into tb_historia_clinica (id_paciente, id_usuario_administrador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado) values
(1, 1, 'estable con tratamiento', 'hipertensión controlada', 'apendicectomía en 1985', 'alergia a la penicilina', 'baja en sodio', '2024-05-10', 'el paciente responde bien al tratamiento actual.', 'activo'),
(2, 1, 'en recuperación', 'fractura de cadera', 'ninguno relevante', 'sin alergias conocidas', 'dieta alta en calcio y fibra', '2024-06-20', 'requiere terapia física dos veces por semana.', 'activo');

-- asignación de enfermedades y medicamentos
insert into tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, fecha_diagnostico, estado) values
(1, 2, '2010-01-15', 'activo');

insert into tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, dosis, frecuencia, instrucciones, estado) values
(1, 2, '50 mg', 'una vez al día', 'tomar por la mañana con el desayuno.', 'activo');


-- 3. inserción de actividades
insert into tb_actividad (id_paciente, id_usuario_administrador, tipo_actividad, descripcion_actividad, fecha_actividad, hora_inicio, hora_fin, estado_actividad) values
(1, 1, 'caminata ligera', 'caminata de 20 minutos por el jardín del centro.', '2024-07-15', '09:00:00', '09:20:00', 'pendiente'),
(2, 1, 'terapia ocupacional', 'sesión de ejercicios de motricidad fina.', '2024-07-16', '11:00:00', '12:00:00', 'pendiente');

use gericare_connect;
select * from tb_usuario; 
select * from tb_rol;
select * from tb_telefono;
select * from tb_paciente;
select * from tb_entrada_salida_paciente;
select * from tb_historia_clinica;
select * from tb_enfermedad;
select * from tb_medicamento;
select * from tb_historia_clinica_medicamento;
select * from tb_historia_clinica_enfermedad;
select * from tb_paciente_asignado;
select * from tb_actividad;