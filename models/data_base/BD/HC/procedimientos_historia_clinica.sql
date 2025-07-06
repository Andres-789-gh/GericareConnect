USE gericare_connect;

-- Borrar procedimientos para asegurar una actualización limpia
DROP PROCEDURE IF EXISTS `crear_historia_base`;
DROP PROCEDURE IF EXISTS `mostrar_historias_clinicas`;
DROP PROCEDURE IF EXISTS `obtener_historia_clinica_por_id`;
DROP PROCEDURE IF EXISTS `actualizar_historia_clinica_completa`;
DROP PROCEDURE IF EXISTS `desactivar_historia_clinica`;
DROP PROCEDURE IF EXISTS `obtener_medicamentos_por_historia`;
DROP PROCEDURE IF EXISTS `obtener_enfermedades_por_historia`;

DELIMITER $$

-- Crea solo la historia base y devuelve su ID.
CREATE PROCEDURE `crear_historia_base`(
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

-- Lee todas las historias para la tabla principal.
CREATE PROCEDURE `mostrar_historias_clinicas`()
BEGIN
    SELECT
        hc.id_historia_clinica,
        DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        (SELECT GROUP_CONCAT(m.nombre_medicamento SEPARATOR ', ')
         FROM tb_historia_clinica_medicamento hcm
         JOIN tb_medicamento m ON hcm.id_medicamento = m.id_medicamento
         WHERE hcm.id_historia_clinica = hc.id_historia_clinica AND hcm.estado = 'Activo') AS medicamentos,
        (SELECT GROUP_CONCAT(e.nombre_enfermedad SEPARATOR ', ')
         FROM tb_historia_clinica_enfermedad hce
         JOIN tb_enfermedad e ON hce.id_enfermedad = e.id_enfermedad
         WHERE hce.id_historia_clinica = hc.id_historia_clinica AND hce.estado = 'Activo') AS enfermedades
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    WHERE hc.estado = 'Activo'
    ORDER BY hc.id_historia_clinica DESC;
END$$

-- **CORRECCIÓN:** Obtener una historia clínica específica por su ID.
CREATE PROCEDURE `obtener_historia_clinica_por_id`(IN p_id_historia_clinica INT)
BEGIN
    SELECT
        hc.id_historia_clinica, hc.id_paciente,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        hc.estado_salud, hc.condiciones, hc.antecedentes_medicos, hc.alergias,
        hc.dietas_especiales, hc.observaciones
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    WHERE hc.id_historia_clinica = p_id_historia_clinica AND hc.estado = 'Activo';
END$$

-- Actualizar una historia completa.
CREATE PROCEDURE `actualizar_historia_clinica_completa`(
    IN p_id_historia_clinica INT,
    IN p_estado_salud TEXT,
    IN p_condiciones TEXT,
    IN p_antecedentes_medicos TEXT,
    IN p_alergias TEXT,
    IN p_dietas_especiales TEXT,
    IN p_observaciones TEXT,
    IN p_medicamentos_ids VARCHAR(255),
    IN p_enfermedades_ids VARCHAR(255)
)
BEGIN
    UPDATE tb_historia_clinica SET
        estado_salud = p_estado_salud, condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos, alergias = p_alergias,
        dietas_especiales = p_dietas_especiales, observaciones = p_observaciones,
        fecha_ultima_consulta = CURDATE()
    WHERE id_historia_clinica = p_id_historia_clinica;

    UPDATE tb_historia_clinica_medicamento SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
    IF p_medicamentos_ids IS NOT NULL AND p_medicamentos_ids != '' THEN
        SET @sql_med = CONCAT('INSERT INTO tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, estado) SELECT ', p_id_historia_clinica, ', id, ''Activo'' FROM JSON_TABLE(''[', REPLACE(p_medicamentos_ids, ',', '],[') ,']'', ''$[*]'' COLUMNS(id INT PATH ''$'')) AS jt');
        PREPARE stmt_med FROM @sql_med;
        EXECUTE stmt_med;
        DEALLOCATE PREPARE stmt_med;
    END IF;

    UPDATE tb_historia_clinica_enfermedad SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
    IF p_enfermedades_ids IS NOT NULL AND p_enfermedades_ids != '' THEN
        SET @sql_enf = CONCAT('INSERT INTO tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, estado) SELECT ', p_id_historia_clinica, ', id, ''Activo'' FROM JSON_TABLE(''[', REPLACE(p_enfermedades_ids, ',', '],[') ,']'', ''$[*]'' COLUMNS(id INT PATH ''$'')) AS jt');
        PREPARE stmt_enf FROM @sql_enf;
        EXECUTE stmt_enf;
        DEALLOCATE PREPARE stmt_enf;
    END IF;
END$$

-- Desactivar una historia clínica.
CREATE PROCEDURE `desactivar_historia_clinica`(IN p_id_historia_clinica INT)
BEGIN
    UPDATE tb_historia_clinica SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
    UPDATE tb_historia_clinica_medicamento SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
    UPDATE tb_historia_clinica_enfermedad SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
END$$

-- Procedimientos auxiliares para la vista de edición.
CREATE PROCEDURE `obtener_medicamentos_por_historia`(IN p_id_historia_clinica INT)
BEGIN
    SELECT m.id_medicamento as id, m.nombre_medicamento as nombre
    FROM tb_historia_clinica_medicamento hcm
    JOIN tb_medicamento m ON hcm.id_medicamento = m.id_medicamento
    WHERE hcm.id_historia_clinica = p_id_historia_clinica AND hcm.estado = 'Activo';
END$$

CREATE PROCEDURE `obtener_enfermedades_por_historia`(IN p_id_historia_clinica INT)
BEGIN
    SELECT e.id_enfermedad as id, e.nombre_enfermedad as nombre
    FROM tb_historia_clinica_enfermedad hce
    JOIN tb_enfermedad e ON hce.id_enfermedad = e.id_enfermedad
    WHERE hce.id_historia_clinica = p_id_historia_clinica AND hce.estado = 'Activo';
END$$

DELIMITER ;