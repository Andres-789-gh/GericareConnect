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

-- 1. LIMPIEZA DE LA TABLA PACIENTE: Si la columna 'alergias' existe, la elimina.
IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'gericare_connect' AND TABLE_NAME = 'tb_paciente' AND COLUMN_NAME = 'alergias')
THEN
    ALTER TABLE `tb_paciente` DROP COLUMN `alergias`;
END IF;

-- 2. BORRADO DE PROCEDIMIENTOS ANTERIORES: Eliminamos las versiones viejas para instalar las nuevas.
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;
DROP PROCEDURE IF EXISTS desactivar_paciente;
DROP PROCEDURE IF EXISTS admin_consulta_global;

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

-- DESACTIVAR un paciente (borrado lógico).
CREATE PROCEDURE `desactivar_paciente`(IN p_id_paciente INT)
BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END//


-- PROCEDIMIENTO PARA LA BÚSQUEDA GLOBAL DEL ADMIN
CREATE PROCEDURE `admin_consulta_global`(IN p_filtro_tipo VARCHAR(50), IN p_busqueda VARCHAR(100), IN p_id_admin_actual INT)
BEGIN
    -- Lógica de búsqueda que ya tenías, pero verificada y limpia.
    IF p_filtro_tipo IN ('Familiar', 'Cuidador', 'Administrador') THEN
        SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, u.fecha_nacimiento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto, '' as genero FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE r.nombre_rol = p_filtro_tipo AND u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSEIF p_filtro_tipo = 'Paciente' THEN
        SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, p.fecha_nacimiento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto, p.genero FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSE
        (SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, u.fecha_nacimiento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto, '' as genero FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')))
        UNION ALL
        (SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, p.fecha_nacimiento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto, p.genero FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')));
    END IF;
END//

-- Devolvemos el delimitador a la normalidad.
DELIMITER ;
