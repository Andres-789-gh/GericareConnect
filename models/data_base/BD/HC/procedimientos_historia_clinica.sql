/*
drop procedure registrar_historia_clinica;
drop procedure consultar_historia_clinica;
drop procedure actualizar_historia_clinica;
drop procedure eliminar_historia_clinica;
*/
delimiter $$

-- =============================================
-- procedimiento para registrar una historia clínica
-- =============================================
create procedure registrar_historia_clinica(
    in p_id_paciente int,
    in p_id_usuario_administrador int,
    in p_estado_salud text,
    in p_condiciones text,
    in p_antecedentes_medicos text,
    in p_alergias text,
    in p_dietas_especiales text,
    in p_fecha_ultima_consulta date,
    in p_observaciones text
)
begin
    insert into tb_historia_clinica (
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
    values (
        p_id_paciente,
        p_id_usuario_administrador,
        p_estado_salud,
        p_condiciones,
        p_antecedentes_medicos,
        p_alergias,
        p_dietas_especiales,
        p_fecha_ultima_consulta,
        p_observaciones,
        'Activo' -- por defecto, se crea como Activo
    );
end$$

-- =============================================
-- procedimiento para consultar historias clínicas
-- se puede consultar por id de historia o por id de paciente
-- =============================================
-- Elimina el procedimiento antiguo si existe
DROP PROCEDURE IF EXISTS consultar_historia_clinica;

-- Cambia el delimitador para crear el nuevo procedimiento
-- Primero, eliminamos el procedimiento anterior para evitar conflictos.
DROP PROCEDURE IF EXISTS consultar_historia_clinica;

-- Cambiamos el delimitador para poder escribir el procedimiento.
DELIMITER $$

-- Creamos el nuevo procedimiento, más limpio y eficiente.
CREATE PROCEDURE `consultar_historia_clinica`(
    IN p_id_historia_clinica INT,
    IN p_busqueda VARCHAR(100)
)
BEGIN
    -- CASO 1: Si se pide un ID específico (para la página de edición).
    IF p_id_historia_clinica IS NOT NULL THEN
        SELECT
            hc.*, -- Trae todos los campos de la historia clínica para el formulario
            CONCAT(p.nombre, ' ', p.apellido) AS paciente_nombre_completo
        FROM
            tb_historia_clinica AS hc
        JOIN
            tb_paciente AS p ON hc.id_paciente = p.id_paciente
        WHERE
            hc.id_historia_clinica = p_id_historia_clinica;

    -- CASO 2: Si no se pide un ID, se busca por texto (para la tabla principal).
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

-- Se restablece el delimitador.
DELIMITER ;


-- =============================================
-- procedimiento para actualizar una historia clínica
-- =============================================
create procedure actualizar_historia_clinica(
    in p_id_historia_clinica int,
    in p_id_usuario_administrador int,
    in p_estado_salud text,
    in p_condiciones text,
    in p_antecedentes_medicos text,
    in p_alergias text,
    in p_dietas_especiales text,
    in p_fecha_ultima_consulta date,
    in p_observaciones text,
    in p_estado enum('Activo','Inactivo')
)
begin
    update tb_historia_clinica
    set
        id_usuario_administrador = p_id_usuario_administrador,
        estado_salud = p_estado_salud,
        condiciones = p_condiciones,
        antecedentes_medicos = p_antecedentes_medicos,
        alergias = p_alergias,
        dietas_especiales = p_dietas_especiales,
        fecha_ultima_consulta = p_fecha_ultima_consulta,
        observaciones = p_observaciones,
        estado = p_estado
    where
        id_historia_clinica = p_id_historia_clinica;
end$$

-- =============================================
-- procedimiento para eliminar (lógicamente) una historia clínica
-- =============================================
create procedure eliminar_historia_clinica(
    in p_id_historia_clinica int
)
begin
    -- se realiza una eliminación lógica cambiando el estado a 'Inactivo'
    update tb_historia_clinica
    set estado = 'Inactivo'
    where id_historia_clinica = p_id_historia_clinica;
end$$

delimiter ;