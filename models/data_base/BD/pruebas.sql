-- insertar paciente 1, relacionado con el familiar de id_usuario = 3
insert into tb_paciente 
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar) values
(2001, 'elena', 'valdez', '1945-10-15', 'Femenino', '3112223344', 'Viuda', 'O+', 3);

-- insertar paciente 2, relacionado con el familiar de id_usuario = 4
insert into tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar) values
(2002, 'roberto', 'solis', '1940-05-20', 'Masculino', '3223334455', 'Casado', 'A+', 3);

-- insertar paciente 3, sin familiar asignado (para que el admin lo vea pero los familiares no)
insert into tb_paciente
(documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, id_usuario_familiar) values
(2003, 'lucia', 'fernandez', '1950-01-30', 'Femenino', '3001112233', 'Soltera', 'B-', null);

select * from tb_paciente_asignado;
insert into tb_paciente_asignado
(id_usuario_cuidador, id_paciente, id_usuario_administrador, descripcion) values
(2, 1, 1, 'Cuidado general y monitoreo de medicación.'),
(2, 3, 1, 'Asistencia en actividades diarias y terapia física.');

-- Insert medicamento
INSERT INTO tb_medicamento (nombre_medicamento, descripcion_medicamento, estado) VALUES
('Paracetamol', 'Analgésico y antipirético', 'Activo'),
('Losartán', 'Medicamento para la presión arterial', 'Activo'),
('Insulina Glargina', 'Para el control de la diabetes', 'Activo');

-- Insert enefermedades
INSERT INTO tb_enfermedad (nombre_enfermedad, descripcion_enfermedad, estado) VALUES
('Diabetes Mellitus Tipo 2', 'Enfermedad crónica que afecta la forma en que el cuerpo procesa el azúcar en la sangre', 'Activo'),
('Hipertensión Arterial', 'Condición crónica en la que la presión en las arterias es consistentemente alta', 'Activo'),
('Artrosis de Rodilla', 'Enfermedad degenerativa de las articulaciones', 'Activo');

-- Insert paciente
INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado) VALUES 
(123456789, 'Elena', 'Díaz', '1945-11-20', 'Femenino', '3109876543', 'Viuda', 'O+', 'Seguros Vitality', 'SGV-789012', 3, 'Activo');