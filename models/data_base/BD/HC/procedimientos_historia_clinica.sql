-- Usar la base de datos correcta
USE gericare_connect;

-- Eliminar procedimientos para asegurar una instalación limpia
DROP PROCEDURE IF EXISTS registrar_historia_clinica;
DROP PROCEDURE IF EXISTS consultar_historia_clinica;
DROP PROCEDURE IF EXISTS actualizar_historia_clinica;
DROP PROCEDURE IF EXISTS eliminar_historia_clinica;
DROP PROCEDURE IF EXISTS consultar_enfermedades_hc;
DROP PROCEDURE IF EXISTS asignar_enfermedad_hc;
DROP PROCEDURE IF EXISTS eliminar_enfermedad_hc;
DROP PROCEDURE IF EXISTS consultar_medicamentos_hc;
DROP PROCEDURE IF EXISTS asignar_medicamento_hc;
DROP PROCEDURE IF EXISTS actualizar_medicamento_hc;
DROP PROCEDURE IF EXISTS eliminar_medicamento_hc;
DROP PROCEDURE IF EXISTS consultar_reporte_completo_hc;

DELIMITER $$

-- =============================================
-- CRUD BÁSICO DE HISTORIA CLÍNICA
-- =============================================

CREATE PROCEDURE `registrar_historia_clinica`(IN p_id_paciente INT, IN p_id_usuario_administrador INT, IN p_estado_salud TEXT, IN p_condiciones TEXT, IN p_antecedentes_medicos TEXT, IN p_alergias TEXT, IN p_dietas_especiales TEXT, IN p_fecha_ultima_consulta DATE, IN p_observaciones TEXT)
BEGIN
    INSERT INTO tb_historia_clinica (id_paciente, id_usuario_administrador, estado_salud, condiciones, antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado)
    VALUES (p_id_paciente, p_id_usuario_administrador, p_estado_salud, p_condiciones, p_antecedentes_medicos, p_alergias, p_dietas_especiales, p_fecha_ultima_consulta, p_observaciones, 'Activo');
END$$

-- MODIFICADO: Ahora cuenta las asignaciones para el "botón inteligente"
CREATE PROCEDURE `consultar_historia_clinica`(IN p_id_historia_clinica INT, IN p_busqueda VARCHAR(100))
BEGIN
    IF p_id_historia_clinica IS NOT NULL THEN
        SELECT hc.*, CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo
        FROM tb_historia_clinica AS hc
        JOIN tb_paciente AS p ON hc.id_paciente = p.id_paciente
        WHERE hc.id_historia_clinica = p_id_historia_clinica;
    ELSE
        SELECT
            hc.id_historia_clinica,
            CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
            DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
            hc.estado_salud,
            (SELECT COUNT(*) FROM tb_historia_clinica_medicamento WHERE id_historia_clinica = hc.id_historia_clinica AND estado = 'Activo') AS med_count,
            (SELECT COUNT(*) FROM tb_historia_clinica_enfermedad WHERE id_historia_clinica = hc.id_historia_clinica AND estado = 'Activo') AS enf_count
        FROM tb_historia_clinica AS hc
        JOIN tb_paciente AS p ON hc.id_paciente = p.id_paciente
        WHERE hc.estado = 'Activo'
            AND (p_busqueda IS NULL OR p_busqueda = '' OR
                 p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
                 p.apellido LIKE CONCAT('%', p_busqueda, '%') or
                 p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'))
        ORDER BY hc.id_historia_clinica DESC;
    END IF;
END$$

CREATE PROCEDURE `actualizar_historia_clinica`(IN p_id_historia_clinica INT, IN p_id_usuario_administrador INT, IN p_estado_salud TEXT, IN p_condiciones TEXT, IN p_antecedentes_medicos TEXT, IN p_alergias TEXT, IN p_dietas_especiales TEXT, IN p_fecha_ultima_consulta DATE, IN p_observaciones TEXT)
BEGIN
    UPDATE tb_historia_clinica SET id_usuario_administrador = p_id_usuario_administrador, estado_salud = p_estado_salud, condiciones = p_condiciones, antecedentes_medicos = p_antecedentes_medicos, alergias = p_alergias, dietas_especiales = p_dietas_especiales, fecha_ultima_consulta = p_fecha_ultima_consulta, observaciones = p_observaciones
    WHERE id_historia_clinica = p_id_historia_clinica;
END$$

CREATE PROCEDURE `eliminar_historia_clinica`(IN p_id_historia_clinica INT)
BEGIN
    UPDATE tb_historia_clinica SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
END$$

-- =============================================
-- GESTIÓN DE ASIGNACIONES
-- =============================================

CREATE PROCEDURE `consultar_enfermedades_hc`(IN p_id_historia_clinica INT)
BEGIN
    SELECT hce.id_hc_enfermedad, e.id_enfermedad, e.nombre_enfermedad FROM tb_historia_clinica_enfermedad AS hce JOIN tb_enfermedad AS e ON hce.id_enfermedad = e.id_enfermedad WHERE hce.id_historia_clinica = p_id_historia_clinica AND hce.estado = 'Activo';
END$$

CREATE PROCEDURE `asignar_enfermedad_hc`(IN p_id_historia_clinica INT, IN p_id_enfermedad INT)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM tb_historia_clinica_enfermedad WHERE id_historia_clinica = p_id_historia_clinica AND id_enfermedad = p_id_enfermedad AND estado = 'Activo') THEN
        INSERT INTO tb_historia_clinica_enfermedad (id_historia_clinica, id_enfermedad, fecha_diagnostico, estado) VALUES (p_id_historia_clinica, p_id_enfermedad, CURDATE(), 'Activo');
        SELECT LAST_INSERT_ID() AS id_asignacion;
    ELSE
        SELECT 0 AS id_asignacion;
    END IF;
END$$

CREATE PROCEDURE `eliminar_enfermedad_hc`(IN p_id_hc_enfermedad INT)
BEGIN
    DELETE FROM tb_historia_clinica_enfermedad WHERE id_hc_enfermedad = p_id_hc_enfermedad;
END$$

CREATE PROCEDURE `consultar_medicamentos_hc`(IN p_id_historia_clinica INT)
BEGIN
    SELECT hcm.id_hc_medicamento, m.id_medicamento, m.nombre_medicamento, hcm.dosis, hcm.frecuencia, hcm.instrucciones FROM tb_historia_clinica_medicamento AS hcm JOIN tb_medicamento AS m ON hcm.id_medicamento = m.id_medicamento WHERE hcm.id_historia_clinica = p_id_historia_clinica AND hcm.estado = 'Activo';
END$$

CREATE PROCEDURE `asignar_medicamento_hc`(IN p_id_historia_clinica INT, IN p_id_medicamento INT, IN p_dosis VARCHAR(100), IN p_frecuencia VARCHAR(100), IN p_instrucciones VARCHAR(250))
BEGIN
    INSERT INTO tb_historia_clinica_medicamento (id_historia_clinica, id_medicamento, dosis, frecuencia, instrucciones, estado) VALUES (p_id_historia_clinica, p_id_medicamento, p_dosis, p_frecuencia, p_instrucciones, 'Activo');
    SELECT LAST_INSERT_ID() AS id_asignacion;
END$$

CREATE PROCEDURE `actualizar_medicamento_hc`(IN p_id_hc_medicamento INT, IN p_dosis VARCHAR(100), IN p_frecuencia VARCHAR(100), IN p_instrucciones VARCHAR(250))
BEGIN
    UPDATE tb_historia_clinica_medicamento SET dosis = p_dosis, frecuencia = p_frecuencia, instrucciones = p_instrucciones WHERE id_hc_medicamento = p_id_hc_medicamento;
END$$

CREATE PROCEDURE `eliminar_medicamento_hc`(IN p_id_hc_medicamento INT)
BEGIN
    DELETE FROM tb_historia_clinica_medicamento WHERE id_hc_medicamento = p_id_hc_medicamento;
END$$

-- =============================================
-- NUEVO PROCEDIMIENTO PARA EL REPORTE COMPLETO
-- =============================================
CREATE PROCEDURE `consultar_reporte_completo_hc`(IN p_id_historia_clinica INT)
BEGIN
    SELECT
        -- Datos de la historia
        hc.*,
        -- Datos del paciente
        p.nombre AS paciente_nombre,
        p.apellido AS paciente_apellido,
        p.documento_identificacion AS paciente_documento,
        p.fecha_nacimiento AS paciente_fecha_nacimiento,
        -- Datos del administrador que la gestionó
        u.nombre AS admin_nombre,
        u.apellido AS admin_apellido
    FROM
        tb_historia_clinica AS hc
    JOIN
        tb_paciente AS p ON hc.id_paciente = p.id_paciente
    LEFT JOIN
        tb_usuario AS u ON hc.id_usuario_administrador = u.id_usuario
    WHERE
        hc.id_historia_clinica = p_id_historia_clinica;
END$$

DELIMITER ;

/*buscar desde el view de cuidador*/
DELIMITER $$

CREATE PROCEDURE `consultar_historias_cuidador`(IN p_id_cuidador INT, IN p_busqueda VARCHAR(100))
BEGIN
    SELECT
        hc.id_historia_clinica,
        CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo,
        DATE_FORMAT(hc.fecha_ultima_consulta, '%d/%m/%Y') AS fecha_formateada,
        hc.estado_salud,
        (SELECT COUNT(*) FROM tb_historia_clinica_medicamento WHERE id_historia_clinica = hc.id_historia_clinica AND estado = 'Activo') AS med_count,
        (SELECT COUNT(*) FROM tb_historia_clinica_enfermedad WHERE id_historia_clinica = hc.id_historia_clinica AND estado = 'Activo') AS enf_count
    FROM
        tb_paciente_asignado pa
    JOIN
        tb_historia_clinica hc ON pa.id_paciente = hc.id_paciente
    JOIN
        tb_paciente p ON pa.id_paciente = p.id_paciente
    WHERE
        pa.id_usuario_cuidador = p_id_cuidador
        AND pa.estado = 'Activo'
        AND hc.estado = 'Activo'
        AND (p_busqueda IS NULL OR p_busqueda = '' OR
             p.nombre LIKE CONCAT('%', p_busqueda, '%') OR
             p.apellido LIKE CONCAT('%', p_busqueda, '%') OR
             p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
END$$

DELIMITER ;