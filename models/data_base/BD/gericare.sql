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

-- -----------------------------------------------------------------------------

insert into tb_rol (nombre_rol, descripcion_rol) values
('Administrador', 'Accede a todas las funciones del sistema'),
('Cuidador', 'Gestiona actividades, tratamientos e historias clínicas'),
('Familiar', 'Realiza solicitudes y ve la información de su paciente');

insert into tb_usuario (tipo_documento, documento_identificacion, nombre, apellido, direccion, correo_electronico, contraseña, estado, id_rol, fecha_contratacion, tipo_contrato, contacto_emergencia, fecha_nacimiento, parentesco) values
-- Administrador (contraseña: admin123)
('CC', 1001, 'Ana', 'Gómez', 'Calle 1 #23-45', 'ana.admin@gmail.com', '$2y$10$kiQHmmtxTynxjAF.QeT21OxI3NUbZAHnJsjq3yZAAcyBRXFpgLk0S', 'Activo', 1, '2020-01-01', 'Término indefinido', '3123456789', '1980-05-10', null),

-- Cuidador (contraseña: cuidador123)
('CC', 1002, 'Luis', 'Pérez', 'Carrera 7 #89-12', 'luis.cuidador@gmail.com', '$2y$10$2tbfrRKBOajFHVlwylOnVOhmni.6wAiSXXyVS2.lBPBniveFy14ga', 'Activo', 2, '2021-03-20', 'Por prestación', '3009876543', '1990-07-15', null),

-- Familiar (contraseña: familiar123)
('CC', 1003, 'Marta', 'Ramírez', 'Transversal 45 #67-89', 'marta.familiar@gmail.com', '$2y$10$9BnxfTQhCtdstW0J3cLTGuD/ZBi.7MS/aQMC0yJhez0DibM.CJa6m', 'Activo', 3, null, null, null, null, 'Madre');
 
/*  */
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