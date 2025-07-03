-- insertar paciente 1, relacionado con el familiar de id_usuario = 3
insert into tb_paciente 
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
values
(2001, 'elena', 'valdez', '1945-10-15', 'Femenino', '3112223344', 'Viuda', 'O+', 3);

-- insertar paciente 2, relacionado con el familiar de id_usuario = 4
insert into tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
values
(2002, 'roberto', 'solis', '1940-05-20', 'Masculino', '3223334455', 'Casado', 'A+', 4);

-- insertar paciente 3, sin familiar asignado (para que el admin lo vea pero los familiares no)
insert into tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
values
(2003, 'lucia', 'fernandez', '1950-01-30', 'Femenino', '3001112233', 'Soltera', 'B-', null);


select * from tb_paciente_asignado;
insert into tb_paciente_asignado
(id_usuario_cuidador, id_paciente, id_usuario_administrador, descripcion)
values
(2, 1, 1, 'Cuidado general y monitoreo de medicación.'),
(2, 3, 1, 'Asistencia en actividades diarias y terapia física.');

-- 3. asignar un paciente al nuevo cuidador con id_usuario = 6 (laura giraldo)
insert into tb_paciente_asignado
(id_usuario_cuidador, id_paciente, id_usuario_administrador, descripcion)
values
(6, 2, 1, 'Monitoreo de signos vitales y acompañamiento.');




-- Paciente 4, asignado al familiar con id_usuario = 3
INSERT INTO tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
VALUES
(2004, 'Carlos', 'Jiménez', '1952-08-25', 'Masculino', '3158887766', 'Casado', 'AB+', 3);

-- Paciente 5, asignado al familiar con id_usuario = 4
INSERT INTO tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
VALUES
(2005, 'Sofia', 'Rivera', '1948-12-01', 'Femenino', '3109998877', 'Soltera', 'B+', 4);

-- Paciente 6, sin familiar asignado, para probar casos donde el admin lo gestiona directamente
INSERT INTO tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar)
VALUES
(2006, 'Isabel', 'Moreno', '1961-02-14', 'Femenino', '3215556677', 'Divorciada', 'A-', NULL);

UPDATE tb_paciente
SET estado = 'Activo'
WHERE id_paciente = 5;