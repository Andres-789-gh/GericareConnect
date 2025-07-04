-- LE DECIMOS A MYSQL QUE VAMOS A TRABAJAR EN ESTA BASE DE DATOS
USE gericare_connect;

-- Borramos los procedimientos si ya existen para evitar errores
DROP PROCEDURE IF EXISTS admin_consulta_global;
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS desactivar_paciente;

-- Cambiamos el delimitador para poder escribir los procedimientos
DELIMITER //

-- === PROCEDIMIENTO PARA LA BÚSQUEDA GLOBAL ===
CREATE PROCEDURE admin_consulta_global(
    IN p_filtro_tipo VARCHAR(50),
    IN p_busqueda VARCHAR(100),
    IN p_id_admin_actual INT
)
BEGIN
    IF p_filtro_tipo IN ('Familiar', 'Cuidador', 'Administrador') THEN
        SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE r.nombre_rol = p_filtro_tipo AND u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSEIF p_filtro_tipo = 'Paciente' THEN
        SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSE
        (SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')))
        UNION ALL
        (SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')));
    END IF;
END //

-- === PROCEDIMIENTOS PARA EL CRUD DE PACIENTES ===
CREATE PROCEDURE registrar_paciente(IN p_documento_identificacion INT, IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_fecha_nacimiento DATE, IN p_genero ENUM('Masculino','Femenino'), IN p_contacto_emergencia VARCHAR(20), IN p_estado_civil VARCHAR(30), IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN p_seguro_medico VARCHAR(100), IN p_numero_seguro VARCHAR(50), IN p_alergias TEXT, IN p_id_usuario_familiar INT)
BEGIN
    INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, alergias, id_usuario_familiar, estado)
    VALUES (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_alergias, p_id_usuario_familiar, 'Activo');
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END//

CREATE PROCEDURE consultar_pacientes(IN p_busqueda VARCHAR(100))
BEGIN
    SELECT * FROM tb_paciente p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END//

CREATE PROCEDURE actualizar_paciente(IN p_id_paciente INT, IN p_documento_identificacion INT, IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_fecha_nacimiento DATE, IN p_genero ENUM('Masculino','Femenino'), IN p_contacto_emergencia VARCHAR(20), IN p_estado_civil VARCHAR(30), IN p_tipo_sangre ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN p_seguro_medico VARCHAR(100), IN p_numero_seguro VARCHAR(50), IN p_alergias TEXT, IN p_id_usuario_familiar INT)
BEGIN
    UPDATE tb_paciente SET documento_identificacion = p_documento_identificacion, nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, genero = p_genero, contacto_emergencia = p_contacto_emergencia, estado_civil = p_estado_civil, tipo_sangre = p_tipo_sangre, seguro_medico = p_seguro_medico, numero_seguro = p_numero_seguro, alergias = p_alergias, id_usuario_familiar = p_id_usuario_familiar WHERE id_paciente = p_id_paciente;
END//

CREATE PROCEDURE desactivar_paciente(IN p_id_paciente INT)
BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END//

-- Devolvemos el delimitador a la normalidad
DELIMITER ;





-- Le decimos a MySQL que si ya existe un procedimiento con este nombre, lo borre primero.
DROP PROCEDURE IF EXISTS admin_consulta_global;

DELIMITER //

-- Creamos el procedimiento para la búsqueda global.
CREATE PROCEDURE admin_consulta_global(
    IN p_filtro_tipo VARCHAR(50),
    IN p_busqueda VARCHAR(100),
    IN p_id_admin_actual INT
)
BEGIN
    -- Si se filtra por un rol de usuario específico
    IF p_filtro_tipo IN ('Familiar', 'Cuidador', 'Administrador') THEN
        SELECT
            u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento,
            CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto
        FROM tb_usuario AS u
        JOIN tb_rol AS r ON u.id_rol = r.id_rol
        WHERE
            r.nombre_rol = p_filtro_tipo
            AND u.estado = 'Activo'
            AND u.id_usuario != p_id_admin_actual
            AND (
                p_busqueda IS NULL OR p_busqueda = '' OR
                u.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                u.apellido LIKE CONCAT('%', p_busqueda, '%') OR
                u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')
            );
    -- Si se filtra por "Paciente"
    ELSEIF p_filtro_tipo = 'Paciente' THEN
        SELECT
            p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento,
            CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto
        FROM tb_paciente AS p
        WHERE
            p.estado = 'Activo'
            AND (
                p_busqueda IS NULL OR p_busqueda = '' OR
                p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
                p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')
            );
    -- Si no hay filtro (buscar en todos)
    ELSE
        (SELECT
            u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento,
            CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto
        FROM tb_usuario AS u
        JOIN tb_rol AS r ON u.id_rol = r.id_rol
        WHERE
            u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual
            AND (
                p_busqueda IS NULL OR p_busqueda = '' OR
                u.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                u.apellido LIKE CONCAT('%', p_busqueda, '%') OR
                u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')
            )
        )
        UNION ALL
        (SELECT
            p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento,
            CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto
        FROM tb_paciente AS p
        WHERE
            p.estado = 'Activo'
            AND (
                p_busqueda IS NULL OR p_busqueda = '' OR
                p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
                p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')
            )
        );
    END IF;
END //

DELIMITER ;
DELETE FROM tb_paciente WHERE documento_identificacion = 2147483647;
SELECT * FROM tb_usuario WHERE id_rol = 3;
INSERT INTO tb_usuario (tipo_documento, documento_identificacion, nombre, apellido, direccion, correo_electronico, contraseña, id_rol, estado)
VALUES ('CC', '999999', 'Familiar', 'De Prueba', 'Calle Falsa 123', 'familiar@prueba.com', 'una_clave_cualquiera', 3, 'Activo');


-- Usamos la base de datos correcta
USE gericare_connect;

-- Modificamos la tabla para añadir el campo de alergias
ALTER TABLE tb_paciente ADD COLUMN alergias TEXT NULL AFTER numero_seguro;

-- Borramos los procedimientos antiguos para instalar las nuevas versiones
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;

-- Cambiamos el delimitador para crear los procedimientos
DELIMITER //

-- PROCEDIMIENTO PARA REGISTRAR (CREATE) VERSIÓN PRO
CREATE PROCEDURE registrar_paciente(
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
    IN p_alergias TEXT,
    IN p_id_usuario_familiar INT
)
BEGIN
    INSERT INTO tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero,
        contacto_emergencia, estado_civil, tipo_sangre, seguro_medico,
        numero_seguro, alergias, id_usuario_familiar, estado
    )
    VALUES (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero,
        p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico,
        p_numero_seguro, p_alergias, p_id_usuario_familiar, 'Activo'
    );
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END//

-- PROCEDIMIENTO PARA ACTUALIZAR (UPDATE) VERSIÓN PRO
CREATE PROCEDURE actualizar_paciente(
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
    IN p_alergias TEXT,
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
        alergias = p_alergias,
        id_usuario_familiar = p_id_usuario_familiar
    WHERE id_paciente = p_id_paciente;
END//

-- Volvemos el delimitador a la normalidad
DELIMITER ;
-- Le decimos a la base de datos que vamos a usar gericare_connect
USE gericare_connect;

-- Borramos los procedimientos si ya existen para evitar errores
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS desactivar_paciente;

-- Cambiamos el delimitador para poder escribir los procedimientos
DELIMITER //

-- 1. PROCEDIMIENTO PARA REGISTRAR (CREATE)
CREATE PROCEDURE registrar_paciente(
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
    INSERT INTO tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero,
        contacto_emergencia, estado_civil, tipo_sangre, seguro_medico,
        numero_seguro, id_usuario_familiar, estado
    )
    VALUES (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero,
        p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico,
        p_numero_seguro, p_id_usuario_familiar, 'Activo'
    );
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END//

-- 2. PROCEDIMIENTO PARA CONSULTAR (READ)
CREATE PROCEDURE consultar_pacientes(
    IN p_busqueda VARCHAR(100)
)
BEGIN
    SELECT *
    FROM tb_paciente p
    WHERE p.estado = 'Activo'
      AND (p_busqueda IS NULL OR p_busqueda = '' OR
           p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
           p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
           p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END//

-- 3. PROCEDIMIENTO PARA ACTUALIZAR (UPDATE)
CREATE PROCEDURE actualizar_paciente(
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

-- 4. PROCEDIMIENTO PARA DESACTIVAR (DELETE)
CREATE PROCEDURE desactivar_paciente(
    IN p_id_paciente INT
)
BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END//

-- Devolvemos el delimitador a la normalidad
DELIMITER ;
-- Si ya existe un procedimiento con este nombre, lo borra para evitar el error.
DROP PROCEDURE IF EXISTS consultar_pacientes;

-- Volvemos a definir el delimitador para crear el procedimiento.
DELIMITER //

-- Creamos la versión final y correcta del procedimiento.
CREATE PROCEDURE consultar_pacientes(
    IN p_busqueda VARCHAR(100)
)
BEGIN
    SELECT *
    FROM tb_paciente p
    WHERE p.estado = 'Activo'
      AND (p_busqueda IS NULL OR p_busqueda = '' OR
           p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
           p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
           p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END //

DELIMITER ;
DELIMITER //
CREATE PROCEDURE consultar_pacientes(
    IN p_busqueda VARCHAR(100)
)
BEGIN
    SELECT *
    FROM tb_paciente p
    WHERE p.estado = 'Activo'
      AND (p_busqueda IS NULL OR p_busqueda = '' OR
           p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
           p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
           p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END //
DELIMITER ;
-- Este comando crea el procedimiento almacenado que tu PHP está buscando.
DELIMITER //
CREATE PROCEDURE consultar_pacientes(
    -- Acepta un parámetro de búsqueda para filtrar resultados.
    IN p_busqueda VARCHAR(100)
)
BEGIN
    -- Selecciona todos los pacientes que están 'Activos'.
    SELECT *
    FROM tb_paciente p
    WHERE p.estado = 'Activo'
      -- Si el término de búsqueda no está vacío, filtra por nombre, apellido o documento.
      AND (p_busqueda IS NULL OR p_busqueda = '' OR
           p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
           p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
           p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END //
DELIMITER ;
-- Este comando actualiza al usuario con el documento 1001.
UPDATE tb_usuario
SET
    -- Asegura que el tipo de documento sea 'CC'.
    tipo_documento = 'CC',
    
    -- Asegura que el estado del usuario sea 'Activo' para que pueda iniciar sesión.
    estado = 'Activo',
    
    -- Asegura que el rol asignado sea el '1', que corresponde al Administrador.
    id_rol = 1
WHERE
    -- Especifica que solo se debe modificar la fila del usuario con este documento.
    documento_identificacion = 1001;
    USE gericare_connect;

-- Eliminar procedimientos antiguos para asegurar una instalación limpia
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS consultar_pacientes;
DROP PROCEDURE IF EXISTS desactivar_paciente;

DELIMITER //

-- PROCEDIMIENTO PARA CREAR (REGISTRAR) UN PACIENTE
CREATE PROCEDURE registrar_paciente(
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
    -- Se insertan los datos en la tabla de pacientes
    INSERT INTO tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero,
        contacto_emergencia, estado_civil, tipo_sangre, seguro_medico,
        numero_seguro, id_usuario_familiar, estado
    )
    VALUES (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero,
        p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico,
        p_numero_seguro, p_id_usuario_familiar, 'Activo'
    );
    -- Devuelve el ID del paciente que se acaba de crear
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END //

-- PROCEDIMIENTO PARA LEER (CONSULTAR) PACIENTES
CREATE PROCEDURE consultar_pacientes(
    IN p_busqueda VARCHAR(100)
)
BEGIN
    -- Selecciona todos los pacientes activos
    -- Si hay un término de búsqueda, filtra por nombre, apellido o documento
    SELECT *
    FROM tb_paciente p
    WHERE p.estado = 'Activo'
      AND (p_busqueda IS NULL OR p_busqueda = '' OR
           p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
           p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
           p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END //

-- PROCEDIMIENTO PARA ACTUALIZAR (EDITAR) UN PACIENTE
CREATE PROCEDURE actualizar_paciente(
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
    -- Actualiza los campos del paciente que coincida con el ID
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
END //

-- PROCEDIMIENTO PARA BORRAR (DESACTIVAR) UN PACIENTE
CREATE PROCEDURE desactivar_paciente(
    IN p_id_paciente INT
)
BEGIN
    -- Cambia el estado del paciente a 'Inactivo' en lugar de borrarlo (borrado lógico)
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END //

DELIMITER ;
USE gericare_connect;

-- Eliminar procedimientos existentes para evitar errores
DROP PROCEDURE IF EXISTS registrar_paciente;
DROP PROCEDURE IF EXISTS actualizar_paciente;
DROP PROCEDURE IF EXISTS consultar_paciente;
DROP PROCEDURE IF EXISTS desactivar_paciente;

DELIMITER //

-- CREATE
CREATE PROCEDURE registrar_paciente(
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
    INSERT INTO tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero,
        contacto_emergencia, estado_civil, tipo_sangre, seguro_medico,
        numero_seguro, id_usuario_familiar, estado
    )
    VALUES (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero,
        p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico,
        p_numero_seguro, p_id_usuario_familiar, 'Activo'
    );
    SELECT LAST_INSERT_ID() AS id_paciente_creado;
END //

-- READ
CREATE PROCEDURE consultar_paciente(
    IN p_id_paciente INT,
    IN p_documento_identificacion INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50)
)
BEGIN
    SELECT
        p.*,
        u.nombre AS nombre_familiar,
        u.apellido AS apellido_familiar,
        u.correo_electronico AS correo_familiar
    FROM tb_paciente p
    LEFT JOIN tb_usuario u ON p.id_usuario_familiar = u.id_usuario
    WHERE p.estado = 'Activo'
        AND (p_id_paciente IS NULL OR p.id_paciente = p_id_paciente)
        AND (p_documento_identificacion IS NULL OR p.documento_identificacion = p_documento_identificacion)
        AND (p_nombre IS NULL OR p.nombre LIKE CONCAT('%', p_nombre, '%'))
        AND (p_apellido IS NULL OR p.apellido LIKE CONCAT('%', p_apellido, '%'));
END //

-- UPDATE
CREATE PROCEDURE actualizar_paciente(
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
    IN p_id_usuario_familiar INT,
    IN p_estado ENUM('Activo', 'Inactivo')
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
        id_usuario_familiar = p_id_usuario_familiar,
        estado = p_estado
    WHERE id_paciente = p_id_paciente;
END //

-- DELETE (desactivación lógica)
CREATE PROCEDURE desactivar_paciente(
    IN p_id_paciente INT
)
BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END //

DELIMITER ;