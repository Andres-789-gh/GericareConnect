USE gericare_connect;

-- Procedimiento para crear una historia clínica y sus relaciones (V2)
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
    -- Cursores para iterar sobre las listas de IDs
    DECLARE cur_medicamentos CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_medicamentos_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_medicamentos_ids) - LENGTH(REPLACE(p_medicamentos_ids, ',', '')));
    DECLARE cur_enfermedades CURSOR FOR SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_enfermedades_ids, ',', numbers.n), ',', -1) AS UNSIGNED) FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers WHERE numbers.n <= 1 + (LENGTH(p_enfermedades_ids) - LENGTH(REPLACE(p_enfermedades_ids, ',', '')));
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Insertar en la tabla principal (la fecha se inserta automáticamente con NOW())
    INSERT INTO tb_historia_clinica (id_paciente, id_usuario_cuidador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado)
    VALUES (p_id_paciente, p_id_usuario_cuidador, p_estado_salud, p_condiciones, p_antecedentes_medicos, p_alergias, p_dietas_especiales, CURDATE(), p_observaciones, 'Activo');

    SET v_id_historia_clinica = LAST_INSERT_ID();

    -- Insertar medicamentos
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

    -- Insertar enfermedades
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
        hc.fecha_ultima_consulta,
        p.nombre AS nombre_paciente,
        p.apellido AS apellido_paciente,
        u.nombre AS nombre_cuidador,
        u.apellido AS apellido_cuidador,
        (SELECT GROUP_CONCAT(m.nombre_medicamento SEPARATOR ', ') FROM tb_historia_clinica_medicamento hcm JOIN tb_medicamento m ON hcm.id_medicamento = m.id_medicamento WHERE hcm.id_historia_clinica = hc.id_historia_clinica) AS medicamentos,
        (SELECT GROUP_CONCAT(e.nombre_enfermedad SEPARATOR ', ') FROM tb_historia_clinica_enfermedad hce JOIN tb_enfermedad e ON hce.id_enfermedad = e.id_enfermedad WHERE hce.id_historia_clinica = hc.id_historia_clinica) AS enfermedades
    FROM tb_historia_clinica hc
    JOIN tb_paciente p ON hc.id_paciente = p.id_paciente
    LEFT JOIN tb_usuario u ON hc.id_usuario_cuidador = u.id_usuario
    WHERE hc.estado = 'Activo';
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