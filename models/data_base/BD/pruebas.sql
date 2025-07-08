use gericare_connect;

-- 1. inserción de pacientes
insert into tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado) values 
(11223344, 'carlos', 'santana', '1948-07-20', 'masculino', '3105556677', 'soltero', 'a+', 'sura eps', '12345-a', 3, 'activo'),
(55667788, 'beatriz', 'pinzón', '1952-03-15', 'femenino', '3201112233', 'casada', 'o-', 'compensar eps', '67890-b', null, 'activo');

-- asignación de los pacientes al cuidador
insert into tb_paciente_asignado (id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion, estado) values
(2, 1, 1, 'monitoreo de presión arterial y asistencia en movilidad.', 'activo'),
(2, 1, 2, 'cuidado post-operatorio y administración de medicamentos.', 'activo');

-- 2. inserción de historias clínicas
insert into tb_historia_clinica (id_paciente, id_usuario_administrador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado) values
(1, 1, 'estable con tratamiento', 'hipertensión controlada', 'apendicectomía en 1985', 'alergia a la penicilina', 'baja en sodio', '2024-05-10', 'el paciente responde bien al tratamiento actual.', 'activo'),
(2, 1, 'en recuperación', 'fractura de cadera', 'ninguno relevante', 'sin alergias conocidas', 'dieta alta en calcio y fibra', '2024-06-20', 'requiere terapia física dos veces por semana.', 'activo');

-- asignación de enfermedades y medicamentos
insert into tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, fecha_diagnostico, estado) values
(1, 2, '2010-01-15', 'activo');

insert into tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, dosis, frecuencia, instrucciones, estado) values
(1, 2, '50 mg', 'una vez al día', 'tomar por la mañana con el desayuno.', 'activo');

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

-- 3. inserción de actividades
insert into tb_actividad (id_paciente, id_usuario_administrador, tipo_actividad, descripcion_actividad, fecha_actividad, hora_inicio, hora_fin, estado_actividad) values
(1, 1, 'caminata ligera', 'caminata de 20 minutos por el jardín del centro.', '2024-07-15', '09:00:00', '09:20:00', 'pendiente'),
(2, 1, 'terapia ocupacional', 'sesión de ejercicios de motricidad fina.', '2024-07-16', '11:00:00', '12:00:00', 'pendiente');
