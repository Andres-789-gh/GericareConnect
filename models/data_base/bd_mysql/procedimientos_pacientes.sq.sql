USE gericare_connect;

-- Eliminar procedimientos existentes
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
