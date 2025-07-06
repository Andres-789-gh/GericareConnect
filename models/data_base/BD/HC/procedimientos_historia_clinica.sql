USE gericare_connect;

-- Borrado seguro de procedimientos para una instalación limpia
DROP PROCEDURE IF EXISTS `crear_historia_clinica_simple`;
DROP PROCEDURE IF EXISTS `mostrar_historias_clinicas_simple`;
DROP PROCEDURE IF EXISTS `obtener_historia_clinica_por_id_simple`;
DROP PROCEDURE IF EXISTS `actualizar_historia_clinica_simple`;
DROP PROCEDURE IF EXISTS `desactivar_historia_clinica_simple`;

DELIMITER $$

-- 1. CREAR una historia clínica (solo datos principales)
CREATE PROCEDURE `crear_historia_clinica_simple`(
    IN p_id_paciente INT,
    IN p_id_usuario_administrador INT,
    IN p_estado_salud TEXT,
    IN p_condiciones TEXT,
    IN p_antecedentes_medicos TEXT,
    IN p_alergias TEXT,
    IN p_dietas_especiales TEXT,
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO tb_historia_clinica (
        id_paciente, id_usuario_administrador, estado_salud, condiciones,
        antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta,
        observaciones, estado
    ) VALUES (
        p_id_paciente, p_id_usuario_administrador, p_estado_salud, p_condiciones,
        p_antecedentes_medicos, p_alergias, p_dietas_especiales, CURDATE(),
        p_observaciones, 'Activo'
    );
    SELECT LAST_INSERT_ID() AS id_historia_clinica_creada;
END$$

-- 2. MOSTRAR el listado de historias clínicas
CREATE PROCEDURE `mostrar_historias_clinicas_simple`()
BEGIN
    SELECT
        hc.id_historia_clinica,
        DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        CONCAT(u.nombre, ' ', u.apellido) AS administrador_nombre_completo,
        hc.estado_salud
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    LEFT JOIN tb_usuario u ON hc.id_usuario_administrador = u.id_usuario
    WHERE hc.estado = 'Activo'
    ORDER BY hc.id_historia_clinica DESC;
END$$

-- 3. OBTENER una única historia clínica para editarla
CREATE PROCEDURE `obtener_historia_clinica_por_id_simple`(IN p_id_historia_clinica INT)
BEGIN
    SELECT
        hc.id_historia_clinica,
        hc.id_paciente,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        hc.estado_salud,
        hc.condiciones,
        hc.antecedentes_medicos,
        hc.alergias,
        hc.dietas_especiales,
        hc.observaciones
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    WHERE hc.id_historia_clinica = p_id_historia_clinica AND hc.estado = 'Activo';
END$$

-- 4. ACTUALIZAR los datos de una historia clínica
CREATE PROCEDURE `actualizar_historia_clinica_simple`(
    IN p_id_historia_clinica INT,
    IN p_estado_salud TEXT,
    IN p_condiciones TEXT,
    IN p_antecedentes_medicos TEXT,
    IN p_alergias TEXT,
    IN p_dietas_especiales TEXT,
    IN p_observaciones TEXT
)
BEGIN
    UPDATE tb_historia_clinica SET
        estado_salud = p_estado_salud,
        condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos,
        alergias = p_alergias,
        dietas_especiales = p_dietas_especiales,
        observaciones = p_observaciones,
        fecha_ultima_consulta = CURDATE()
    WHERE id_historia_clinica = p_id_historia_clinica;
END$$

-- 5. DESACTIVAR (borrado lógico) una historia clínica
CREATE PROCEDURE `desactivar_historia_clinica_simple`(IN p_id_historia_clinica INT)
BEGIN
    UPDATE tb_historia_clinica SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
END$$

DELIMITER ;