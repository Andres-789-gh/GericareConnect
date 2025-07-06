-- Le decimos a MySQL que vamos a trabajar en esta base de datos
USE gericare_connect;

-- Borramos los procedimientos viejos para instalar las nuevas versiones
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;

-- Cambiamos el delimitador para poder escribir los procedimientos
DELIMITER //

-- === PROCEDIMIENTO PARA REGISTRAR (SIN ALERGIAS) ===
CREATE PROCEDURE `registrar_paciente`(
    IN p_documento_identificacion INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_fecha_nacimiento DATE,
    IN p_genero ENUM('Masculino','Femenino'),
    IN p_contacto_emergencia VARCHAR(20),
    IN p_estado_civil VARCHAR(30),
    IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    IN p_seguro_medico VARCHAR(100),
    IN p_numero_seguro VARCHAR(50),
    IN p_id_usuario_familiar INT
)
BEGIN
    INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado)
    VALUES (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo');
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END//

-- === PROCEDIMIENTO PARA ACTUALIZAR (SIN ALERGIAS) ===
CREATE PROCEDURE `actualizar_paciente`(
    IN p_id_paciente INT,
    IN p_documento_identificacion INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_fecha_nacimiento DATE,
    IN p_genero ENUM('Masculino','Femenino'),
    IN p_contacto_emergencia VARCHAR(20),
    IN p_estado_civil VARCHAR(30),
    IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    IN p_seguro_medico VARCHAR(100),
    IN p_numero_seguro VARCHAR(50),
    IN p_id_usuario_familiar INT
)
BEGIN
    UPDATE tb_paciente SET
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
    WHERE id_paciente = p_id_paciente;
END//

-- Devolvemos el delimitador a la normalidad
DELIMITER ;
-- Le decimos a MySQL que vamos a trabajar en esta base de datos para evitar errores.
USE gericare_connect;

-- 2. BORRADO DE PROCEDIMIENTOS ANTERIORES: Eliminamos las versiones viejas para instalar las nuevas.
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;

-- Cambiamos el delimitador para poder escribir los procedimientos complejos.
DELIMITER //

-- 3. PROCEDIMIENTOS CRUD DE PACIENTES (VERSIÓN FINAL)

-- CREAR un paciente (versión corregida sin alergias).
CREATE PROCEDURE `registrar_paciente`(
    IN p_documento_identificacion INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_fecha_nacimiento DATE,
    IN p_genero ENUM('Masculino','Femenino'),
    IN p_contacto_emergencia VARCHAR(20),
    IN p_estado_civil VARCHAR(30),
    IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    IN p_seguro_medico VARCHAR(100),
    IN p_numero_seguro VARCHAR(50),
    IN p_id_usuario_familiar INT
)
BEGIN
    INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado)
    VALUES (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo');
END//

-- ACTUALIZAR un paciente (versión corregida sin alergias).
CREATE PROCEDURE `actualizar_paciente`(
    IN p_id_paciente INT,
    IN p_documento_identificacion INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_fecha_nacimiento DATE,
    IN p_genero ENUM('Masculino','Femenino'),
    IN p_contacto_emergencia VARCHAR(20),
    IN p_estado_civil VARCHAR(30),
    IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    IN p_seguro_medico VARCHAR(100),
    IN p_numero_seguro VARCHAR(50),
    IN p_id_usuario_familiar INT
)
BEGIN
    UPDATE tb_paciente SET
        documento_identificacion = p_documento_identificacion, nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, genero = p_genero, contacto_emergencia = p_contacto_emergencia, estado_civil = p_estado_civil, tipo_sangre = p_tipo_sangre, seguro_medico = p_seguro_medico, numero_seguro = p_numero_seguro, id_usuario_familiar = p_id_usuario_familiar
    WHERE id_paciente = p_id_paciente;
END//

-- CONSULTAR los pacientes para mostrarlos en la tabla del administrador.
CREATE PROCEDURE `consultar_pacientes`()
BEGIN
    -- Se calcula la edad directamente en la consulta para mayor eficiencia.
    SELECT *, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad 
    FROM tb_paciente 
    WHERE estado = 'Activo' ORDER BY apellido, nombre;
END//



-- Devolvemos el delimitador a la normalidad.
DELIMITER ;
