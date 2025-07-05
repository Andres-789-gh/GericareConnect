-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-07-2025 a las 18:22:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gericare_connect`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarpaciente` (IN `p_id_paciente` INT, IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_genero` ENUM('Masculino','Femenino'), IN `p_contacto_emergencia` VARCHAR(20), IN `p_estado_civil` VARCHAR(30), IN `p_tipo_sangre` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN `p_seguro_medico` VARCHAR(100), IN `p_numero_seguro` VARCHAR(50), IN `p_id_usuario_familiar` INT, IN `p_estado` ENUM('Activo','Inactivo'))   begin
    update tb_paciente set
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
    where id_paciente = p_id_paciente;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizarusuariogenerico` (IN `p_id_usuario` INT, IN `p_tipo_documento` ENUM('CC','CE','PA'), IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_direccion` VARCHAR(250), IN `p_correo_electronico` VARCHAR(100), IN `p_estado` ENUM('Activo','Inactivo'), IN `p_contraseña_hash` VARCHAR(255), IN `p_fecha_contratacion` DATE, IN `p_tipo_contrato` VARCHAR(50), IN `p_contacto_emergencia` VARCHAR(20), IN `p_parentesco` VARCHAR(50))   begin
    update tb_usuario set
        tipo_documento = p_tipo_documento,
        documento_identificacion = p_documento_identificacion,
        nombre = p_nombre,
        apellido = p_apellido,
        fecha_nacimiento = p_fecha_nacimiento,
        direccion = p_direccion,
        correo_electronico = p_correo_electronico,
        estado = p_estado,
        contraseña_hash = p_contraseña_hash,
        fecha_contratacion = p_fecha_contratacion,
        tipo_contrato = p_tipo_contrato,
        contacto_emergencia = p_contacto_emergencia,
        parentesco = p_parentesco
    where id_usuario = p_id_usuario;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_paciente` (IN `p_id_paciente` INT, IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_genero` ENUM('Masculino','Femenino'), IN `p_contacto_emergencia` VARCHAR(20), IN `p_estado_civil` VARCHAR(30), IN `p_tipo_sangre` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN `p_seguro_medico` VARCHAR(100), IN `p_numero_seguro` VARCHAR(50), IN `p_id_usuario_familiar` INT)   BEGIN
    UPDATE tb_paciente SET
        documento_identificacion = p_documento_identificacion, nombre = p_nombre, apellido = p_apellido, fecha_nacimiento = p_fecha_nacimiento, genero = p_genero, contacto_emergencia = p_contacto_emergencia, estado_civil = p_estado_civil, tipo_sangre = p_tipo_sangre, seguro_medico = p_seguro_medico, numero_seguro = p_numero_seguro, id_usuario_familiar = p_id_usuario_familiar
    WHERE id_paciente = p_id_paciente;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_consulta_global` (IN `p_filtro_tipo` VARCHAR(50), IN `p_busqueda` VARCHAR(100), IN `p_id_admin_actual` INT)   BEGIN
    IF p_filtro_tipo IN ('Familiar', 'Cuidador', 'Administrador') THEN
        SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, u.fecha_nacimiento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto, '' as genero FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE r.nombre_rol = p_filtro_tipo AND u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSEIF p_filtro_tipo = 'Paciente' THEN
        SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, p.fecha_nacimiento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto, p.genero FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%'));
    ELSE
        (SELECT u.id_usuario AS id, 'Usuario' AS tipo_entidad, u.documento_identificacion AS documento, u.fecha_nacimiento, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo, r.nombre_rol AS rol, u.correo_electronico AS contacto, '' as genero FROM tb_usuario AS u JOIN tb_rol AS r ON u.id_rol = r.id_rol WHERE u.estado = 'Activo' AND u.id_usuario != p_id_admin_actual AND (p_busqueda IS NULL OR p_busqueda = '' OR u.nombre LIKE CONCAT('%', p_busqueda, '%') OR u.apellido LIKE CONCAT('%', p_busqueda, '%') OR u.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')))
        UNION ALL
        (SELECT p.id_paciente AS id, 'Paciente' AS tipo_entidad, p.documento_identificacion AS documento, p.fecha_nacimiento, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo, 'Paciente' AS rol, p.contacto_emergencia AS contacto, p.genero FROM tb_paciente AS p WHERE p.estado = 'Activo' AND (p_busqueda IS NULL OR p_busqueda = '' OR p.nombre LIKE CONCAT('%', p_busqueda, '%') OR p.apellido LIKE CONCAT('%', p_busqueda, '%') OR p.documento_identificacion LIKE CONCAT('%', p_busqueda, '%')));
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `asignarpacienteacuidador` (IN `p_id_usuario_cuidador` INT, IN `p_id_usuario_administrador` INT, IN `p_id_paciente` INT, IN `p_descripcion` VARCHAR(250))   begin
    insert into tb_paciente_asignado (
        id_usuario_cuidador, id_usuario_administrador, id_paciente, descripcion, estado
    ) values (
        p_id_usuario_cuidador, p_id_usuario_administrador, p_id_paciente, p_descripcion, 'Activo'
    );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancelaractividad` (IN `p_id_actividad` INT)   begin
    update tb_actividad set estado_actividad = 'Cancelada' where id_actividad = p_id_actividad;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancelartratamiento` (IN `p_id_tratamiento` INT)   begin
    update tb_tratamiento set estado_tratamiento = 'Cancelado' where id_tratamiento = p_id_tratamiento;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultarhistoriaclinicaporpaciente` (IN `p_id_paciente` INT)   begin
    select hc.*, p.nombre as nombre_paciente, p.apellido as apellido_paciente,
           u.nombre as nombre_cuidador, u.apellido as apellido_cuidador
    from tb_historia_clinica hc
    join tb_paciente p on hc.id_paciente = p.id_paciente
    left join tb_usuario u on hc.id_usuario_cuidador = u.id_usuario
    where hc.id_paciente = p_id_paciente and hc.estado = 'Activo';
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultarpaciente` (IN `p_id_paciente` INT, IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50))   begin
    select p.*, u.nombre as nombre_familiar, u.apellido as apellido_familiar
    from tb_paciente p
    left join tb_usuario u on p.id_usuario_familiar = u.id_usuario and u.estado = 'Activo'
    where
        p.estado = 'Activo'
        and (p_id_paciente is null or p.id_paciente = p_id_paciente)
        and (p_documento_identificacion is null or p.documento_identificacion = p_documento_identificacion)
        and (p_nombre is null or p.nombre like concat('%', p_nombre, '%'))
        and (p_apellido is null or p.apellido like concat('%', p_apellido, '%'));
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultarusuario` (IN `p_id_usuario` INT, IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50))   begin
    select u.*, group_concat(r.nombre_rol separator ', ') as roles_asignados
    from tb_usuario u
    left join tb_usuario_rol ur on u.id_usuario = ur.id_usuario and ur.estado = 'Activo'
    left join tb_rol r on ur.id_rol = r.id_rol
    where
        u.estado = 'Activo'
        and (p_id_usuario is null or u.id_usuario = p_id_usuario)
        and (p_documento_identificacion is null or u.documento_identificacion = p_documento_identificacion)
        and (p_nombre is null or u.nombre like concat('%', p_nombre, '%'))
        and (p_apellido is null or u.apellido like concat('%', p_apellido, '%'))
    group by u.id_usuario;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultar_paciente` (IN `p_id_paciente` INT, IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultar_pacientes_cuidador` (IN `p_id_cuidador` INT, IN `p_busqueda` VARCHAR(100))   begin
    select
        p.id_paciente,
        p.documento_identificacion,
        p.nombre,
        p.apellido,
        p.fecha_nacimiento,
        p.genero
    from
        tb_paciente_asignado as pa
    join
        tb_paciente as p on pa.id_paciente = p.id_paciente
    where
        pa.id_usuario_cuidador = p_id_cuidador
        and pa.estado = 'Activo'
        and p.estado = 'Activo'
        and (
            p_busqueda is null or p_busqueda = '' or
            p.nombre like concat('%', p_busqueda, '%') or
            p.apellido like concat('%', p_busqueda, '%') or
            p.documento_identificacion like concat('%', p_busqueda, '%')
        );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultar_pacientes_familiar` (IN `p_id_familiar` INT, IN `p_busqueda` VARCHAR(100))   begin
    select
        p.id_paciente,
        p.documento_identificacion,
        p.nombre,
        p.apellido,
        p.fecha_nacimiento,
        p.genero,
        p.estado
    from
        tb_paciente as p
    where
        p.id_usuario_familiar = p_id_familiar and p.estado = 'Activo'
        and (
            p_busqueda is null or p_busqueda = '' or
            p.nombre like concat('%', p_busqueda, '%') or
            p.apellido like concat('%', p_busqueda, '%') or
            p.documento_identificacion like concat('%', p_busqueda, '%')
        );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultar_solicitudes_familiar` (IN `p_id_familiar` INT, IN `p_busqueda` VARCHAR(100))   begin
    select
        s.id_solicitud,
        s.tipo_solicitud,
        s.motivo_solicitud,
        s.estado_solicitud,
        s.fecha_solicitud
    from
        tb_solicitud as s
    where
        s.id_usuario_familiar = p_id_familiar
        and (
            p_busqueda is null or p_busqueda = '' or
            s.tipo_solicitud like concat('%', p_busqueda, '%') or
            s.motivo_solicitud like concat('%', p_busqueda, '%') or
            s.id_solicitud like concat('%', p_busqueda, '%')
        );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `crear_historia_clinica` (IN `p_id_paciente` INT, IN `p_id_usuario_cuidador` INT, IN `p_estado_salud` TEXT, IN `p_condiciones` TEXT, IN `p_antecedentes_medicos` TEXT, IN `p_alergias` TEXT, IN `p_dietas_especiales` TEXT, IN `p_observaciones` TEXT, IN `p_medicamentos_ids` VARCHAR(255), IN `p_enfermedades_ids` VARCHAR(255))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivarhistoriaclinica` (IN `p_id_historia_clinica` INT)   begin
    update tb_historia_clinica set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
    update tb_historia_clinica_medicamento set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
    update tb_historia_clinica_enfermedad set estado = 'Inactivo' where id_historia_clinica = p_id_historia_clinica;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivarpaciente` (IN `p_id_paciente` INT)   begin
    update tb_paciente set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_historia_clinica set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_tratamiento set estado_tratamiento = 'Cancelado' where id_paciente = p_id_paciente and estado_tratamiento = 'Activo';
    update tb_actividad set estado_actividad = 'Cancelada' where id_paciente = p_id_paciente and estado_actividad = 'Pendiente';
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente = p_id_paciente;
    update tb_solicitud set estado_solicitud = 'Cancelada' where id_paciente = p_id_paciente and estado_solicitud = 'Pendiente';
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivarpacienteasignado` (IN `p_id_paciente_asignado` INT)   begin
    update tb_paciente_asignado set estado = 'Inactivo' where id_paciente_asignado = p_id_paciente_asignado;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivarusuario` (IN `p_id_usuario` INT)   begin
    update tb_usuario set estado = 'Inactivo' where id_usuario = p_id_usuario;
    update tb_usuario_rol set estado = 'Inactivo' where id_usuario = p_id_usuario;
    update tb_telefono set estado = 'Inactivo' where id_usuario = p_id_usuario;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivar_historia_clinica` (IN `p_id_historia_clinica` INT)   BEGIN
    UPDATE tb_historia_clinica SET estado = 'Inactivo' WHERE id_historia_clinica = p_id_historia_clinica;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivar_paciente` (IN `p_id_paciente` INT)   BEGIN
    UPDATE tb_paciente SET estado = 'Inactivo' WHERE id_paciente = p_id_paciente;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `desactivar_usuario` (IN `p_id_usuario_a_desactivar` INT, IN `p_id_admin_actual` INT)   BEGIN
    IF p_id_usuario_a_desactivar != p_id_admin_actual THEN
        UPDATE tb_usuario SET estado = 'Inactivo' WHERE id_usuario = p_id_usuario_a_desactivar;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertaractividad` (IN `p_id_paciente` INT, IN `p_id_usuario_cuidador` INT, IN `p_tipo_actividad` VARCHAR(100), IN `p_descripcion_actividad` TEXT, IN `p_fecha_actividad` DATE, IN `p_hora_inicio` TIME, IN `p_hora_fin` TIME)   begin
    insert into tb_actividad (
        id_paciente, id_usuario_cuidador, tipo_actividad, descripcion_actividad,
        fecha_actividad, hora_inicio, hora_fin, estado_actividad
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_tipo_actividad, p_descripcion_actividad,
        p_fecha_actividad, p_hora_inicio, p_hora_fin, 'Pendiente'
    );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertarhistoriaclinica` (IN `p_id_paciente` INT, IN `p_id_usuario_cuidador` INT, IN `p_estado_salud` TEXT, IN `p_condiciones` TEXT, IN `p_antecedentes_medicos` TEXT, IN `p_alergias` TEXT, IN `p_dietas_especiales` TEXT, IN `p_fecha_ultima_consulta` DATE, IN `p_observaciones` TEXT)   begin
    insert into tb_historia_clinica (
        id_paciente, id_usuario_cuidador, estado_salud, condiciones,
        antecedentes_medicos, alergias, dietas_especiales, fecha_ultima_consulta, observaciones, estado
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_estado_salud, p_condiciones,
        p_antecedentes_medicos, p_alergias, p_dietas_especiales, p_fecha_ultima_consulta, p_observaciones, 'Activo'
    );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertarpaciente` (IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_genero` ENUM('Masculino','Femenino'), IN `p_contacto_emergencia` VARCHAR(20), IN `p_estado_civil` VARCHAR(30), IN `p_tipo_sangre` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN `p_seguro_medico` VARCHAR(100), IN `p_numero_seguro` VARCHAR(50), IN `p_id_usuario_familiar` INT)   begin
    insert into tb_paciente (
        documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia,
        estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado
    ) values (
        p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia,
        p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo'
    );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertartratamiento` (IN `p_id_paciente` INT, IN `p_id_usuario_cuidador` INT, IN `p_id_usuario_administrador` INT, IN `p_descripcion` TEXT, IN `p_instrucciones_especiales` TEXT, IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   begin
    insert into tb_tratamiento (
        id_paciente, id_usuario_cuidador, id_usuario_administrador, descripcion,
        instrucciones_especiales, fecha_inicio, fecha_fin, estado_tratamiento
    ) values (
        p_id_paciente, p_id_usuario_cuidador, p_id_usuario_administrador, p_descripcion,
        p_instrucciones_especiales, p_fecha_inicio, p_fecha_fin, 'Activo'
    );
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertarusuariogenerico` (IN `p_tipo_documento` ENUM('CC','CE','PA'), IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_direccion` VARCHAR(250), IN `p_correo_electronico` VARCHAR(100), IN `p_contraseña_hash` VARCHAR(255), IN `p_fecha_contratacion` DATE, IN `p_tipo_contrato` VARCHAR(50), IN `p_contacto_emergencia` VARCHAR(20), IN `p_parentesco` VARCHAR(50), IN `p_nombre_rol` VARCHAR(50))   begin
    declare v_nuevo_id_usuario int;
    declare v_id_rol int;

    select id_rol into v_id_rol from tb_rol where nombre_rol = p_nombre_rol;

    if v_id_rol is null then
        signal sqlstate '45000' set message_text = 'Error: El rol especificado no existe.';
    end if;

    insert into tb_usuario (
        tipo_documento, documento_identificacion, nombre, apellido, fecha_nacimiento,
        direccion, correo_electronico, contraseña_hash, fecha_contratacion, tipo_contrato,
        contacto_emergencia, parentesco, estado
    ) values (
        p_tipo_documento, p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento,
        p_direccion, p_correo_electronico, p_contraseña_hash, p_fecha_contratacion, p_tipo_contrato,
        p_contacto_emergencia, p_parentesco, 'Activo'
    );

    set v_nuevo_id_usuario = last_insert_id();

    insert into tb_usuario_rol (id_usuario, id_rol) values (v_nuevo_id_usuario, v_id_rol);
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrar_historias_clinicas` ()   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_paciente` (IN `p_documento_identificacion` INT, IN `p_nombre` VARCHAR(50), IN `p_apellido` VARCHAR(50), IN `p_fecha_nacimiento` DATE, IN `p_genero` ENUM('Masculino','Femenino'), IN `p_contacto_emergencia` VARCHAR(20), IN `p_estado_civil` VARCHAR(30), IN `p_tipo_sangre` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'), IN `p_seguro_medico` VARCHAR(100), IN `p_numero_seguro` VARCHAR(50), IN `p_id_usuario_familiar` INT)   BEGIN
    INSERT INTO tb_paciente (documento_identificacion, nombre, apellido, fecha_nacimiento, genero, contacto_emergencia, estado_civil, tipo_sangre, seguro_medico, numero_seguro, id_usuario_familiar, estado)
    VALUES (p_documento_identificacion, p_nombre, p_apellido, p_fecha_nacimiento, p_genero, p_contacto_emergencia, p_estado_civil, p_tipo_sangre, p_seguro_medico, p_numero_seguro, p_id_usuario_familiar, 'Activo');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_actividad`
--

CREATE TABLE `tb_actividad` (
  `id_actividad` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) NOT NULL,
  `tipo_actividad` varchar(100) NOT NULL,
  `descripcion_actividad` text DEFAULT NULL,
  `fecha_actividad` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `estado_actividad` enum('Pendiente','Completada','Cancelada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_enfermedad`
--

CREATE TABLE `tb_enfermedad` (
  `id_enfermedad` int(11) NOT NULL,
  `nombre_enfermedad` varchar(100) NOT NULL,
  `descripcion_enfermedad` varchar(250) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_entrada_salida_paciente`
--

CREATE TABLE `tb_entrada_salida_paciente` (
  `id_entrada_salida_paciente` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) NOT NULL,
  `id_usuario_administrador` int(11) DEFAULT NULL,
  `id_paciente` int(11) NOT NULL,
  `fecha_entrada_salida_paciente` datetime NOT NULL,
  `tipo_movimiento` enum('Entrada','Salida') NOT NULL,
  `motivo_entrada_salida_paciente` varchar(250) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_historia_clinica`
--

CREATE TABLE `tb_historia_clinica` (
  `id_historia_clinica` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) DEFAULT NULL,
  `estado_salud` text DEFAULT NULL,
  `condiciones` text DEFAULT NULL,
  `antecedentes_medicos` text DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `dietas_especiales` text DEFAULT NULL,
  `fecha_ultima_consulta` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_historia_clinica_cirugia`
--

CREATE TABLE `tb_historia_clinica_cirugia` (
  `id_cirugia` int(11) NOT NULL,
  `id_historia_clinica` int(11) NOT NULL,
  `descripcion_cirugia` varchar(250) NOT NULL,
  `fecha_cirugia` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_historia_clinica_enfermedad`
--

CREATE TABLE `tb_historia_clinica_enfermedad` (
  `id_hc_enfermedad` int(11) NOT NULL,
  `id_historia_clinica` int(11) NOT NULL,
  `id_enfermedad` int(11) NOT NULL,
  `fecha_diagnostico` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_historia_clinica_medicamento`
--

CREATE TABLE `tb_historia_clinica_medicamento` (
  `id_hc_medicamento` int(11) NOT NULL,
  `id_historia_clinica` int(11) NOT NULL,
  `id_medicamento` int(11) NOT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `frecuencia` varchar(100) DEFAULT NULL,
  `instrucciones` varchar(250) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_medicamento`
--

CREATE TABLE `tb_medicamento` (
  `id_medicamento` int(11) NOT NULL,
  `nombre_medicamento` varchar(100) NOT NULL,
  `descripcion_medicamento` varchar(250) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_paciente`
--

CREATE TABLE `tb_paciente` (
  `id_paciente` int(11) NOT NULL,
  `documento_identificacion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `contacto_emergencia` varchar(20) NOT NULL,
  `estado_civil` varchar(30) NOT NULL,
  `tipo_sangre` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `seguro_medico` varchar(100) DEFAULT NULL,
  `numero_seguro` varchar(50) DEFAULT NULL,
  `id_usuario_familiar` int(11) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_paciente`
--

INSERT INTO `tb_paciente` (`id_paciente`, `documento_identificacion`, `nombre`, `apellido`, `fecha_nacimiento`, `genero`, `contacto_emergencia`, `estado_civil`, `tipo_sangre`, `seguro_medico`, `numero_seguro`, `id_usuario_familiar`, `estado`) VALUES
(1, 3213, 'fas', 'fas', '0001-12-22', 'Femenino', 'dfas', '13asf1d', 'A-', 'afds', '003', NULL, 'Inactivo'),
(3, 12354, 'XD', 'fads', '0005-05-02', 'Masculino', '13468', 'xd', 'AB+', 'fasd', '34', 3, 'Inactivo'),
(4, 3145, 'FASD', 'AFSD', '0002-02-22', 'Masculino', 'FSDD', 'FASD', 'A+', 'ASFD', '23132', 3, 'Inactivo'),
(5, 341356, 'FASD', 'FADS', '0021-12-23', 'Femenino', 'FASDFDAS', 'FADS', 'A+', 'DFASSF', '0.4', 3, 'Inactivo'),
(6, 346, 'DASF', 'FDAS', '5135-12-05', 'Masculino', 'DASF', 'DAFS', 'A-', 'DSFDA', 'FASD', 3, 'Inactivo'),
(8, 132456, 'CF', 'CF', '0055-05-05', 'Masculino', '45646', 'SOLTERO', 'A-', 'FAS', '132', 3, 'Inactivo'),
(9, 123456789, 'Jose Alberto', 'Valenzuela', '1961-10-24', 'Masculino', '3215984987548', 'casado', 'B-', 'compensar', '123456', 3, 'Activo'),
(10, 321564489, 'Iris', 'Pacheco xd', '1985-11-16', 'Femenino', '313548464894', 'casada', 'A-', 'compensar', '654518', 3, 'Activo'),
(11, 42346464, 'dfasaefv', 'fadsfads', '5464-06-04', 'Masculino', '246464+88', 'fdsalj´fapl', 'B+', 'fdsakllhas', '4563546', 3, 'Inactivo'),
(13, 121212, 'Fernando', 'Villegas', '1990-08-08', 'Masculino', '34513115', 'soltero', 'A+', 'compensar', '123', 3, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_paciente_asignado`
--

CREATE TABLE `tb_paciente_asignado` (
  `id_paciente_asignado` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) NOT NULL,
  `id_usuario_administrador` int(11) DEFAULT NULL,
  `id_paciente` int(11) NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_rol`
--

CREATE TABLE `tb_rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` enum('Familiar','Cuidador','Administrador') NOT NULL,
  `descripcion_rol` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_rol`
--

INSERT INTO `tb_rol` (`id_rol`, `nombre_rol`, `descripcion_rol`) VALUES
(1, 'Administrador', 'Accede a todas las funciones del sistema'),
(2, 'Cuidador', 'Gestiona actividades, tratamientos e historias clínicas'),
(3, 'Familiar', 'Realiza solicitudes y ve la información de su paciente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud`
--

CREATE TABLE `tb_solicitud` (
  `id_solicitud` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario_familiar` int(11) NOT NULL,
  `id_usuario_administrador` int(11) DEFAULT NULL,
  `tipo_solicitud` varchar(100) NOT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `urgencia_solicitud` enum('Baja','Media','Alta','Urgente') NOT NULL,
  `motivo_solicitud` text NOT NULL,
  `estado_solicitud` enum('Pendiente','Aprobada','Rechazada','Cancelada') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_telefono`
--

CREATE TABLE `tb_telefono` (
  `id_telefono` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `numero_telefono` varchar(20) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_tratamiento`
--

CREATE TABLE `tb_tratamiento` (
  `id_tratamiento` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) DEFAULT NULL,
  `id_usuario_administrador` int(11) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `instrucciones_especiales` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado_tratamiento` enum('Activo','Finalizado','Pausado','Cancelado') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_turno`
--

CREATE TABLE `tb_turno` (
  `id_turno` int(11) NOT NULL,
  `id_usuario_cuidador` int(11) NOT NULL,
  `dia_de_la_semana` varchar(20) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL,
  `tipo_documento` enum('CC','CE','PA') NOT NULL,
  `documento_identificacion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `id_rol` int(11) NOT NULL,
  `fecha_contratacion` date DEFAULT NULL,
  `tipo_contrato` varchar(50) DEFAULT NULL,
  `contacto_emergencia` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `parentesco` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_usuario`
--

INSERT INTO `tb_usuario` (`id_usuario`, `tipo_documento`, `documento_identificacion`, `nombre`, `apellido`, `direccion`, `correo_electronico`, `contraseña`, `estado`, `id_rol`, `fecha_contratacion`, `tipo_contrato`, `contacto_emergencia`, `fecha_nacimiento`, `parentesco`) VALUES
(1, 'CC', 1001, 'Ana', 'Gómez', 'Calle 1 #23-45', 'ana.admin@gmail.com', '$2y$10$OXa7oydkzU1RHRIqTmtA/uXWkX5xDiE9qsNtYkTlEqOvsyVyDhP6m', 'Activo', 1, '2020-01-01', 'Término indefinido', '3123456789', '1980-05-10', NULL),
(2, 'CC', 1002, 'Luis', 'Pérez', 'Carrera 7 #89-12', 'luis.cuidador@gmail.com', '$2y$10$hsq/o0VP8FIxRyyWWNYBouWSJwoLyamKXkXq5JZqXoirGkf.GGBay', 'Inactivo', 2, '2021-03-20', 'Por prestación', '3009876543', '1990-07-15', NULL),
(3, 'CC', 1003, 'Marta', 'Ramírez', 'Transversal 45 #67-89', 'marta.familiar@gmail.com', '$2y$10$OySfR10lEMiJczGwRnkKz.qgc7GS8hsvagOoJOiK/4UWwUVflhUji', 'Activo', 3, NULL, NULL, NULL, '1975-03-22', 'Madre');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_actividad`
--
ALTER TABLE `tb_actividad`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `fk_actividad_paciente` (`id_paciente`),
  ADD KEY `fk_actividad_usuario_cuidador` (`id_usuario_cuidador`);

--
-- Indices de la tabla `tb_enfermedad`
--
ALTER TABLE `tb_enfermedad`
  ADD PRIMARY KEY (`id_enfermedad`);

--
-- Indices de la tabla `tb_entrada_salida_paciente`
--
ALTER TABLE `tb_entrada_salida_paciente`
  ADD PRIMARY KEY (`id_entrada_salida_paciente`),
  ADD KEY `fk_entrada_salida_paciente` (`id_paciente`),
  ADD KEY `fk_entrada_salida_usuario_cuidador` (`id_usuario_cuidador`),
  ADD KEY `fk_entrada_salida_usuario_administrador` (`id_usuario_administrador`);

--
-- Indices de la tabla `tb_historia_clinica`
--
ALTER TABLE `tb_historia_clinica`
  ADD PRIMARY KEY (`id_historia_clinica`),
  ADD KEY `fk_historia_clinica_paciente` (`id_paciente`),
  ADD KEY `fk_historia_clinica_usuario_cuidador` (`id_usuario_cuidador`);

--
-- Indices de la tabla `tb_historia_clinica_cirugia`
--
ALTER TABLE `tb_historia_clinica_cirugia`
  ADD PRIMARY KEY (`id_cirugia`),
  ADD KEY `fk_hc_cirugia_historia_clinica` (`id_historia_clinica`);

--
-- Indices de la tabla `tb_historia_clinica_enfermedad`
--
ALTER TABLE `tb_historia_clinica_enfermedad`
  ADD PRIMARY KEY (`id_hc_enfermedad`),
  ADD KEY `fk_hc_enfermedad_historia_clinica` (`id_historia_clinica`),
  ADD KEY `fk_hc_enfermedad_enfermedad` (`id_enfermedad`);

--
-- Indices de la tabla `tb_historia_clinica_medicamento`
--
ALTER TABLE `tb_historia_clinica_medicamento`
  ADD PRIMARY KEY (`id_hc_medicamento`),
  ADD KEY `fk_hc_medicamento_historia_clinica` (`id_historia_clinica`),
  ADD KEY `fk_hc_medicamento_medicamento` (`id_medicamento`);

--
-- Indices de la tabla `tb_medicamento`
--
ALTER TABLE `tb_medicamento`
  ADD PRIMARY KEY (`id_medicamento`);

--
-- Indices de la tabla `tb_paciente`
--
ALTER TABLE `tb_paciente`
  ADD PRIMARY KEY (`id_paciente`),
  ADD UNIQUE KEY `documento_identificacion` (`documento_identificacion`),
  ADD KEY `fk_paciente_usuario_familiar` (`id_usuario_familiar`);

--
-- Indices de la tabla `tb_paciente_asignado`
--
ALTER TABLE `tb_paciente_asignado`
  ADD PRIMARY KEY (`id_paciente_asignado`),
  ADD KEY `fk_paciente_asignado_usuario_cuidador` (`id_usuario_cuidador`),
  ADD KEY `fk_paciente_asignado_usuario_administrador` (`id_usuario_administrador`),
  ADD KEY `fk_paciente_asignado_paciente` (`id_paciente`);

--
-- Indices de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tb_solicitud`
--
ALTER TABLE `tb_solicitud`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_solicitud_paciente` (`id_paciente`),
  ADD KEY `fk_solicitud_usuario_familiar` (`id_usuario_familiar`),
  ADD KEY `fk_solicitud_usuario_administrador` (`id_usuario_administrador`);

--
-- Indices de la tabla `tb_telefono`
--
ALTER TABLE `tb_telefono`
  ADD PRIMARY KEY (`id_telefono`),
  ADD KEY `fk_telefono_usuario` (`id_usuario`);

--
-- Indices de la tabla `tb_tratamiento`
--
ALTER TABLE `tb_tratamiento`
  ADD PRIMARY KEY (`id_tratamiento`),
  ADD KEY `fk_tratamiento_paciente` (`id_paciente`),
  ADD KEY `fk_tratamiento_usuario_cuidador` (`id_usuario_cuidador`),
  ADD KEY `fk_tratamiento_usuario_administrador` (`id_usuario_administrador`);

--
-- Indices de la tabla `tb_turno`
--
ALTER TABLE `tb_turno`
  ADD PRIMARY KEY (`id_turno`),
  ADD KEY `fk_turno_usuario_cuidador` (`id_usuario_cuidador`);

--
-- Indices de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `documento_identificacion` (`documento_identificacion`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_actividad`
--
ALTER TABLE `tb_actividad`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_enfermedad`
--
ALTER TABLE `tb_enfermedad`
  MODIFY `id_enfermedad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_entrada_salida_paciente`
--
ALTER TABLE `tb_entrada_salida_paciente`
  MODIFY `id_entrada_salida_paciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_historia_clinica`
--
ALTER TABLE `tb_historia_clinica`
  MODIFY `id_historia_clinica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_historia_clinica_cirugia`
--
ALTER TABLE `tb_historia_clinica_cirugia`
  MODIFY `id_cirugia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_historia_clinica_enfermedad`
--
ALTER TABLE `tb_historia_clinica_enfermedad`
  MODIFY `id_hc_enfermedad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_historia_clinica_medicamento`
--
ALTER TABLE `tb_historia_clinica_medicamento`
  MODIFY `id_hc_medicamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_medicamento`
--
ALTER TABLE `tb_medicamento`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_paciente`
--
ALTER TABLE `tb_paciente`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tb_paciente_asignado`
--
ALTER TABLE `tb_paciente_asignado`
  MODIFY `id_paciente_asignado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud`
--
ALTER TABLE `tb_solicitud`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_telefono`
--
ALTER TABLE `tb_telefono`
  MODIFY `id_telefono` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_tratamiento`
--
ALTER TABLE `tb_tratamiento`
  MODIFY `id_tratamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_turno`
--
ALTER TABLE `tb_turno`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_actividad`
--
ALTER TABLE `tb_actividad`
  ADD CONSTRAINT `fk_actividad_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_actividad_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_entrada_salida_paciente`
--
ALTER TABLE `tb_entrada_salida_paciente`
  ADD CONSTRAINT `fk_entrada_salida_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_entrada_salida_usuario_administrador` FOREIGN KEY (`id_usuario_administrador`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_entrada_salida_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_historia_clinica`
--
ALTER TABLE `tb_historia_clinica`
  ADD CONSTRAINT `fk_historia_clinica_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_historia_clinica_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_historia_clinica_cirugia`
--
ALTER TABLE `tb_historia_clinica_cirugia`
  ADD CONSTRAINT `fk_hc_cirugia_historia_clinica` FOREIGN KEY (`id_historia_clinica`) REFERENCES `tb_historia_clinica` (`id_historia_clinica`);

--
-- Filtros para la tabla `tb_historia_clinica_enfermedad`
--
ALTER TABLE `tb_historia_clinica_enfermedad`
  ADD CONSTRAINT `fk_hc_enfermedad_enfermedad` FOREIGN KEY (`id_enfermedad`) REFERENCES `tb_enfermedad` (`id_enfermedad`),
  ADD CONSTRAINT `fk_hc_enfermedad_historia_clinica` FOREIGN KEY (`id_historia_clinica`) REFERENCES `tb_historia_clinica` (`id_historia_clinica`);

--
-- Filtros para la tabla `tb_historia_clinica_medicamento`
--
ALTER TABLE `tb_historia_clinica_medicamento`
  ADD CONSTRAINT `fk_hc_medicamento_historia_clinica` FOREIGN KEY (`id_historia_clinica`) REFERENCES `tb_historia_clinica` (`id_historia_clinica`),
  ADD CONSTRAINT `fk_hc_medicamento_medicamento` FOREIGN KEY (`id_medicamento`) REFERENCES `tb_medicamento` (`id_medicamento`);

--
-- Filtros para la tabla `tb_paciente`
--
ALTER TABLE `tb_paciente`
  ADD CONSTRAINT `fk_paciente_usuario_familiar` FOREIGN KEY (`id_usuario_familiar`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_paciente_asignado`
--
ALTER TABLE `tb_paciente_asignado`
  ADD CONSTRAINT `fk_paciente_asignado_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_paciente_asignado_usuario_administrador` FOREIGN KEY (`id_usuario_administrador`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_paciente_asignado_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_solicitud`
--
ALTER TABLE `tb_solicitud`
  ADD CONSTRAINT `fk_solicitud_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_solicitud_usuario_administrador` FOREIGN KEY (`id_usuario_administrador`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_solicitud_usuario_familiar` FOREIGN KEY (`id_usuario_familiar`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_telefono`
--
ALTER TABLE `tb_telefono`
  ADD CONSTRAINT `fk_telefono_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_tratamiento`
--
ALTER TABLE `tb_tratamiento`
  ADD CONSTRAINT `fk_tratamiento_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `tb_paciente` (`id_paciente`),
  ADD CONSTRAINT `fk_tratamiento_usuario_administrador` FOREIGN KEY (`id_usuario_administrador`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_tratamiento_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_turno`
--
ALTER TABLE `tb_turno`
  ADD CONSTRAINT `fk_turno_usuario_cuidador` FOREIGN KEY (`id_usuario_cuidador`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Filtros para la tabla `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `tb_rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
