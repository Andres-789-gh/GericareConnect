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