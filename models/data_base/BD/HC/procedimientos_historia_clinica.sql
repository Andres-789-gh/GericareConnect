USE gericare_connect;

-- Procedimiento para crear una historia clínica y sus relaciones
DROP PROCEDURE IF EXISTS `crear_historia_clinica`;
DELIMITER $$
CREATE PROCEDURE `crear_historia_clinica`(
    IN p_id_paciente INT,
    IN p_id_usuario_cuidador INT,
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
    DECLARE v_id_historia_clinica INT;
    DECLARE v_medicamento_id INT;
    DECLARE v_enfermedad_id INT;
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_medicamentos CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_medicamentos_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_medicamentos_ids) - LENGTH(REPLACE(p_medicamentos_ids, ',', '')));
    DECLARE cur_enfermedades CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_enfermedades_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_enfermedades_ids) - LENGTH(REPLACE(p_enfermedades_ids, ',', '')));
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    INSERT INTO tb_historia_clinica (id_paciente, id_usuario_cuidador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado)
    VALUES (p_id_paciente, p_id_usuario_cuidador, p_estado_salud, p_condiciones, p_antecedentes_medicos, p_alergias, p_dietas_especiales, CURDATE(), p_observaciones, 'Activo');

    SET v_id_historia_clinica = LAST_INSERT_ID();

    IF p_medicamentos_ids IS NOT NULL AND p_medicamentos_ids != '' THEN
        OPEN cur_medicamentos;
        read_loop_med: LOOP
            FETCH cur_medicamentos INTO v_medicamento_id;
            IF done THEN
                LEAVE read_loop_med;
            END IF;
            INSERT INTO tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, estado) VALUES (v_id_historia_clinica, v_medicamento_id, 'Activo');
        END LOOP;
        CLOSE cur_medicamentos;
        SET done = FALSE;
    END IF;

    IF p_enfermedades_ids IS NOT NULL AND p_enfermedades_ids != '' THEN
        OPEN cur_enfermedades;
        read_loop_enf: LOOP
            FETCH cur_enfermedades INTO v_enfermedad_id;
            IF done THEN
                LEAVE read_loop_enf;
            END IF;
            INSERT INTO tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, estado) VALUES (v_id_historia_clinica, v_enfermedad_id, 'Activo');
        END LOOP;
        CLOSE cur_enfermedades;
    END IF;
END$$
DELIMITER ;

-- Procedimiento para leer las historias clínicas
DROP PROCEDURE IF EXISTS `mostrar_historias_clinicas`;
DELIMITER $$
CREATE PROCEDURE `mostrar_historias_clinicas`()
BEGIN
    SELECT
        hc.id_historia_clinica,
        DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        CONCAT(u.nombre, ' ', u.apellido) AS cuidador_nombre_completo,
        hc.estado_salud,
        (SELECT GROUP_CONCAT(m.nombre_medicamento SEPARATOR ', ') FROM tb_historia_clinica_medicamento hcm JOIN tb_medicamento m ON hcm.id_medicamento = m.id_medicamento WHERE hcm.id_historia_clinica = hc.id_historia_clinica) AS medicamentos,
        (SELECT GROUP_CONCAT(e.nombre_enfermedad SEPARATOR ', ') FROM tb_historia_clinica_enfermedad hce JOIN tb_enfermedad e ON hce.id_enfermedad = e.id_enfermedad WHERE hce.id_historia_clinica = hc.id_historia_clinica) AS enfermedades
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    LEFT JOIN tb_usuario u ON hc.id_usuario_cuidador = u.id_usuario
    WHERE hc.estado = 'Activo'
    ORDER BY hc.id_historia_clinica DESC;
END$$
DELIMITER ;

-- Procedimiento para el borrado lógico
DROP PROCEDURE IF EXISTS `desactivar_historia_clinica`;
DELIMITER $$
CREATE PROCEDURE `desactivar_historia_clinica`(IN p_id_historia_clinica INT)
BEGIN
    UPDATE tb_historia_clinica SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
END$$
DELIMITER ;

-- *** NUEVO PROCEDIMIENTO PARA ACTUALIZAR ***
DROP PROCEDURE IF EXISTS `actualizar_historia_clinica_completa`;
DELIMITER $$
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
    DECLARE v_medicamento_id INT;
    DECLARE v_enfermedad_id INT;
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_medicamentos CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_medicamentos_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_medicamentos_ids) - LENGTH(REPLACE(p_medicamentos_ids, ',', '')));
    DECLARE cur_enfermedades CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_enfermedades_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_enfermedades_ids) - LENGTH(REPLACE(p_enfermedades_ids, ',', '')));
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- 1. Actualizar los campos de texto
    UPDATE tb_historia_clinica SET
        estado_salud = p_estado_salud,
        condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos,
        alergias = p_alergias,
        dietas_especiales = p_dietas_especiales,
        observaciones = p_observaciones,
        fecha_ultima_consulta = CURDATE()
    WHERE id_historia_clinica = p_id_historia_clinica;

    -- 2. Limpiar las relaciones antiguas
    DELETE FROM tb_historia_clinica_medicamento WHERE id_historia_clinica = p_id_historia_clinica;
    DELETE FROM tb_historia_clinica_enfermedad WHERE id_historia_clinica = p_id_historia_clinica;

    -- 3. Insertar las nuevas relaciones (similar a la creación)
    IF p_medicamentos_ids IS NOT NULL AND p_medicamentos_ids != '' THEN
        OPEN cur_medicamentos;
        read_loop_med: LOOP
            FETCH cur_medicamentos INTO v_medicamento_id;
            IF done THEN
                LEAVE read_loop_med;
            END IF;
            INSERT INTO tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, estado) VALUES (p_id_historia_clinica, v_medicamento_id, 'Activo');
        END LOOP;
        CLOSE cur_medicamentos;
        SET done = FALSE;
    END IF;

    IF p_enfermedades_ids IS NOT NULL AND p_enfermedades_ids != '' THEN
        OPEN cur_enfermedades;
        read_loop_enf: LOOP
            FETCH cur_enfermedades INTO v_enfermedad_id;
            IF done THEN
                LEAVE read_loop_enf;
            END IF;
            INSERT INTO tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, estado) VALUES (p_id_historia_clinica, v_enfermedad_id, 'Activo');
        END LOOP;
        CLOSE cur_enfermedades;
    END IF;

END$$
DELIMITER ;