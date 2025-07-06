-- Usar la base de datos correcta
USE gericare_connect;

-- Eliminar procedimientos antiguos si existen para asegurar una instalación limpia
DROP PROCEDURE IF EXISTS registrar_historia_clinica;
DROP PROCEDURE IF EXISTS consultar_historia_clinica;
DROP PROCEDURE IF EXISTS actualizar_historia_clinica;
DROP PROCEDURE IF EXISTS eliminar_historia_clinica;

-- Cambiar el delimitador para poder escribir los procedimientos
DELIMITER $$

-- =============================================
-- PROCEDIMIENTO PARA REGISTRAR (CREATE) UNA HISTORIA CLÍNICA
-- =============================================
CREATE PROCEDURE `registrar_historia_clinica`(
    IN p_id_paciente INT,
    IN p_id_usuario_administrador INT,
    IN p_estado_salud TEXT,
    IN p_condiciones TEXT,
    IN p_antecedentes_medicos TEXT,
    IN p_alergias TEXT,
    IN p_dietas_especiales TEXT,
    IN p_fecha_ultima_consulta DATE,
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO tb_historia_clinica (
        id_paciente,
        id_usuario_administrador,
        estado_salud,
        condiciones,
        antecedentes_medicos,
        alergias,
        dietas_especiales,
        fecha_ultima_consulta,
        observaciones,
        estado
    )
    VALUES (
        p_id_paciente,
        p_id_usuario_administrador,
        p_estado_salud,
        p_condiciones,
        p_antecedentes_medicos,
        p_alergias,
        p_dietas_especiales,
        p_fecha_ultima_consulta,
        p_observaciones,
        'Activo' -- Por defecto, se crea como 'Activo'
    );
END$$

-- =============================================
-- PROCEDIMIENTO PARA CONSULTAR (READ) HISTORIAS CLÍNICAS
-- Permite buscar por ID específico o por texto en general.
-- =============================================
CREATE PROCEDURE `consultar_historia_clinica`(
    IN p_id_historia_clinica INT,
    IN p_busqueda VARCHAR(100)
)
BEGIN
    -- Si se provee un ID, se devuelve una sola historia para el formulario de edición.
    IF p_id_historia_clinica IS NOT NULL THEN
        SELECT
            hc.*, -- Trae todos los campos de la historia clínica
            CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo
        FROM
            tb_historia_clinica AS hc
        JOIN
            tb_paciente AS p ON hc.id_paciente = p.id_paciente
        WHERE
            hc.id_historia_clinica = p_id_historia_clinica;

    -- Si no se provee un ID, se devuelve una lista para la tabla principal.
    ELSE
        SELECT
            hc.id_historia_clinica,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
            DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
            hc.estado_salud
        FROM
            tb_historia_clinica AS hc
        JOIN
            tb_paciente AS p ON hc.id_paciente = p.id_paciente
        WHERE
            hc.estado = 'Activo'
            AND (p_busqueda IS NULL OR p_busqueda = '' OR
                 p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                 p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
                 p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'))
        ORDER BY
            hc.id_historia_clinica DESC;
    END IF;
END$$

-- =============================================
-- PROCEDIMIENTO PARA ACTUALIZAR (UPDATE) UNA HISTORIA CLÍNICA
-- =============================================
CREATE PROCEDURE `actualizar_historia_clinica`(
    IN p_id_historia_clinica INT,
    IN p_id_usuario_administrador INT,
    IN p_estado_salud TEXT,
    IN p_condiciones TEXT,
    IN p_antecedentes_medicos TEXT,
    IN p_alergias TEXT,
    IN p_dietas_especiales TEXT,
    IN p_fecha_ultima_consulta DATE,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE tb_historia_clinica
    SET
        id_usuario_administrador = p_id_usuario_administrador,
        estado_salud = p_estado_salud,
        condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos,
        alergias = p_alergias,
        dietas_especiales = p_dietas_especiales,
        fecha_ultima_consulta = p_fecha_ultima_consulta,
        observaciones = p_observaciones
    WHERE
        id_historia_clinica = p_id_historia_clinica;
END$$

-- =============================================
-- PROCEDIMIENTO PARA ELIMINAR (DESACTIVAR) UNA HISTORIA CLÍNICA
-- =============================================
CREATE PROCEDURE `eliminar_historia_clinica`(
    IN p_id_historia_clinica INT
)
BEGIN
    -- Se realiza una eliminación lógica cambiando el estado a 'Inactivo'
    UPDATE tb_historia_clinica
    SET estado = 'Inactivo'
    WHERE id_historia_clinica = p_id_historia_clinica;
END$$

-- Devolver el delimitador a la normalidad
DELIMITER ;