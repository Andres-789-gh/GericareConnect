-- Usamos la base de datos correcta para que todo funcione.
USE gericare_connect;

-- 1. LIMPIEZA DE PROCEDIMIENTOS ANTERIORES
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;
DROP PROCEDURE IF EXISTS desactivar_paciente;
DROP PROCEDURE IF EXISTS registrar_actividad;
DROP PROCEDURE IF EXISTS consultar_actividades_por_cuidador;
DROP PROCEDURE IF EXISTS desactivar_actividad;

-- Cambiamos el delimitador para poder escribir los procedimientos.
DELIMITER //

-- 2. PROCEDIMIENTOS DE PACIENTES (Versión Final y Limpia)
CREATE PROCEDURE `registrar_paciente`(IN p_documento_identificacion INT, IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_fecha_nacimiento DATE, IN p_genero ENUM('Masculino','Femenino'), IN p_contacto_emergencia VARCHAR(20), IN p_estado_civil VARCHAR(30), IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN p_seguro_medico VARCHAR(100), IN p_numero_seguro VARCHAR(50), IN p_id_usuario_familiar INT)
BEGIN
    INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado)
    VALUES (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo');
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END//

CREATE PROCEDURE `actualizar_paciente`(IN p_id_paciente INT, IN p_documento_identificacion INT, IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_fecha_nacimiento DATE, IN p_genero ENUM('Masculino','Femenino'), IN p_contacto_emergencia VARCHAR(20), IN p_estado_civil VARCHAR(30), IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN p_seguro_medico VARCHAR(100), IN p_numero_seguro VARCHAR(50), IN p_id_usuario_familiar INT)
BEGIN
    UPDATE tb_paciente SET documento_identificacion = p_documento_identificacion, nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, genero = p_genero, contacto_emergencia = p_contacto_emergencia, estado_civil = p_estado_civil, tipo_sangre = p_tipo_sangre, seguro_medico = p_seguro_medico, numero_seguro = p_numero_seguro, id_usuario_familiar = p_id_usuario_familiar WHERE id_paciente = p_id_paciente;
END//

CREATE PROCEDURE `consultar_pacientes`()
BEGIN
    SELECT *, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad 
    FROM tb_paciente 
    WHERE estado = 'Activo' ORDER BY apellido, nombre;
END//

CREATE PROCEDURE `desactivar_paciente`(IN p_id_paciente INT)
BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END//


-- 3. NUEVOS PROCEDIMIENTOS PARA ACTIVIDADES
-- Procedimiento para que el cuidador registre una nueva actividad.
CREATE PROCEDURE `registrar_actividad`(
    IN p_id_paciente INT,
    IN p_id_usuario_cuidador INT,
    IN p_tipo_actividad VARCHAR(100),
    IN p_descripcion_actividad TEXT,
    IN p_fecha_actividad DATE,
    IN p_hora_inicio TIME,
    IN p_hora_fin TIME
)
BEGIN
    INSERT INTO tb_actividad (id_paciente, id_usuario_cuidador, tipo_actividad, descripcion_actividad, fecha_actividad, hora_inicio, hora_fin, estado_actividad)
    VALUES (p_id_paciente, p_id_usuario_cuidador, p_tipo_actividad, p_descripcion_actividad, p_fecha_actividad, p_hora_inicio, p_hora_fin, 'Pendiente');
END//

-- Procedimiento para que un cuidador vea TODAS sus actividades asignadas.
CREATE PROCEDURE `consultar_actividades_por_cuidador`(
    IN p_id_usuario_cuidador INT
)
BEGIN
    SELECT 
        a.id_actividad,
        a.tipo_actividad,
        a.descripcion_actividad,
        a.fecha_actividad,
        a.hora_inicio,
        a.estado_actividad,
        p.nombre AS nombre_paciente,
        p.apellido AS apellido_paciente
    FROM tb_actividad a
    JOIN tb_paciente p ON a.id_paciente = p.id_paciente
    WHERE a.id_usuario_cuidador = p_id_usuario_cuidador
    AND a.estado_actividad != 'Cancelada'
    ORDER BY a.fecha_actividad DESC, a.hora_inicio ASC;
END//

-- Procedimiento para cancelar (borrado lógico) una actividad.
CREATE PROCEDURE `desactivar_actividad`(
    IN p_id_actividad INT
)
BEGIN
    UPDATE tb_actividad SET estado_actividad = 'Cancelada' WHERE id_actividad = p_id_actividad;
END//

-- Devolvemos el delimitador a la normalidad.
DELIMITER ;
