use gericare_connect;

-- crear usuarios con contraseñas
create user 'admin_user'@'localhost' identified by 'admin_123';
create user 'cuidador_user'@'localhost' identified by 'cuidador_123';
create user 'familiar_user'@'localhost' identified by 'familiar_123';

-- permisos de administrador
grant all privileges on gericare_connect.* to 'admin_user'@'localhost';

-- permisos de cuidador
grant select, insert, update on gericare_connect.tb_historia_clinica to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_historia_clinica_medicamento to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_historia_clinica_enfermedad to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_historia_clinica_cirugia to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_tratamiento to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_actividad to 'cuidador_user'@'localhost';
grant select, insert, update on gericare_connect.tb_entrada_salida_paciente to 'cuidador_user'@'localhost';
grant select on gericare_connect.tb_paciente to 'cuidador_user'@'localhost';
grant select on gericare_connect.tb_usuario to 'cuidador_user'@'localhost';
grant select on gericare_connect.tb_paciente_asignado to 'cuidador_user'@'localhost';

-- permisos de familiar
grant select, insert, update on gericare_connect.tb_solicitud to 'familiar_user'@'localhost';
grant select, insert, update on gericare_connect.tb_usuario to 'familiar_user'@'localhost';
grant select on gericare_connect.tb_paciente to 'familiar_user'@'localhost';

flush privileges;

-- mostrar permisos
show grants for 'admin_user'@'localhost';
show grants for 'cuidador_user'@'localhost';
show grants for 'familiar_user'@'localhost';
