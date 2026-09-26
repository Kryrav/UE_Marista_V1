-- =====================================================
-- MIGRACIÓN M02 — ITERACIÓN 2: Rematriculación + estados terminales
-- Sistema Marista SS.CC. — Módulo Matrícula / Estudiantes
-- Objetivo F-04, F-09:
--  - Flujo diferenciado de rematriculación (sin reescribir SP de pensiones)
--  - Estados terminales con historial preservado:
--    Confirmado, Inscrito, Pendiente_Documentos (M01),
--    Retirado, Trasladado, Egresado (M02)
-- Compatibilidad: solo ADD NULL; estado_inscripcion sigue VARCHAR libre
-- (sin CHECK para no romper filas históricas). MySQL 9.1: sin IF NOT EXISTS.
-- Aplicar después de M01. Requiere SPs base del script V2.
-- =====================================================

-- 1) Motivo del cambio de estado (auditoría funcional mínima) --
-- Para re-aplicar: ignorar error 1060 si la columna ya existe.
ALTER TABLE `matricula`
    ADD COLUMN `motivo_estado` VARCHAR(255) NULL DEFAULT NULL;

DELIMITER $$

-- 2) Reporte de matrículas: exponer motivo + docs (redefine M01) --
DROP PROCEDURE IF EXISTS `listar_matriculas`$$
CREATE PROCEDURE `listar_matriculas`()
BEGIN
    SELECT m.id_matricula, m.gestion, m.tipo AS tipo_matricula, m.folio,
           m.estado_inscripcion, m.status AS estado_matricula,
           m.plazo_documentos_hasta, m.docs_checklist, m.compromiso_firmado, m.docs_observacion,
           m.motivo_estado,
           e.id_estudiante, p.ci AS ci_estudiante,
           p.nombre AS nombre_estudiante, p.apellido AS apellido_estudiante,
           pa.id_paralelo, CONCAT(pa.nivel, ' - ', pa.grado, pa.sigla) AS curso,
           pa.turno, pa.tutor, pa.cupo, pa.status AS estado_paralelo
    FROM matricula m
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN persona p ON e.id_persona = p.id_persona
    JOIN paralelo pa ON m.id_paralelo = pa.id_paralelo
    ORDER BY m.gestion DESC, pa.nivel, pa.grado, pa.sigla, p.apellido;
END$$

-- 3) HALLAZGO I2: un SP que hace START TRANSACTION y aborta con SIGNAL
-- (duplicado, validación) dejaba la transacción ABIERTA en la conexión:
-- los writes siguientes del mismo request quedaban sin commit (invisibles,
-- con locks) y se perdían al cerrar. Se blinda en dos capas:
--   a) EXIT HANDLER con ROLLBACK+RESIGNAL en los SPs escritores (aquí), y
--   b) rollback de transacción huérfana en Libraries/Core/Mysql.php.
-- NOTA: matricular_estudiante_y_generar_pensiones NO se redefine (su cuerpo
-- vivo tiene mejoras de mensualidades no incluidas en el script V2; la capa b
-- lo cubre). sp_registrar/updateEstudiante sí (cuerpos idénticos a M01).
DROP PROCEDURE IF EXISTS `sp_registrar_estudiante`$$
CREATE PROCEDURE `sp_registrar_estudiante`(
    IN p_ci VARCHAR(30), IN p_nombre VARCHAR(80), IN p_apellido VARCHAR(100),
    IN p_sexo VARCHAR(10), IN p_direccion_dom VARCHAR(100), IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100), IN p_usuario VARCHAR(50), IN p_password TEXT, IN p_id_rol BIGINT,
    IN p_colegio_proc VARCHAR(250), IN p_rude VARCHAR(50), IN p_provincia VARCHAR(80),
    IN p_ciudad VARCHAR(80), IN p_pais VARCHAR(50), IN p_fnacimiento DATE,
    IN p_emergencia VARCHAR(100), IN p_estado_reg VARCHAR(20), IN p_statusGeneral INT,
    IN p_foto VARCHAR(255),
    IN p_folio INT UNSIGNED, IN p_estante VARCHAR(10), IN p_gaveta VARCHAR(20), IN p_estado_legajo VARCHAR(20)
)
BEGIN
    DECLARE v_id_persona BIGINT;
    DECLARE v_usuario_final VARCHAR(50);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado.';
    END IF;
    IF (p_cel IS NOT NULL AND p_cel != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE cel = p_cel) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El celular ya está registrado.';
        END IF;
    END IF;
    IF (p_email IS NOT NULL AND p_email != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE email = p_email) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El email ya está registrado.';
        END IF;
    END IF;
    SET v_usuario_final = NULLIF(TRIM(COALESCE(p_usuario, '')), '');
    IF (v_usuario_final IS NULL OR v_usuario_final = '') THEN
        SET v_usuario_final = TRIM(COALESCE(p_email, ''));
    END IF;
    IF (v_usuario_final IS NULL OR v_usuario_final = '') THEN
        SET v_usuario_final = TRIM(p_ci);
    END IF;
    INSERT INTO persona (ci, nombre, apellido, sexo, direccion_dom, cel, email, usuario, password, id_rol, status)
    VALUES (p_ci, p_nombre, p_apellido, p_sexo,
            NULLIF(TRIM(COALESCE(p_direccion_dom, '')), ''),
            NULLIF(TRIM(COALESCE(p_cel, '')), ''),
            NULLIF(TRIM(COALESCE(p_email, '')), ''),
            v_usuario_final, p_password, p_id_rol, p_statusGeneral);
    SET v_id_persona = LAST_INSERT_ID();
    IF (p_rude IS NOT NULL AND TRIM(COALESCE(p_rude, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE rude = p_rude) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado.';
        END IF;
    END IF;
    IF (p_folio IS NOT NULL) THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE folio_fisico = p_folio) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El folio ya está asignado a otro estudiante.';
        END IF;
    END IF;
    INSERT INTO estudiante (id_persona, colegio_proc, rude, provincia, ciudad, pais, fnacimiento,
                            emergencia, estado_reg, status, foto, folio_fisico, estante, gaveta, estado_legajo)
    VALUES (v_id_persona,
            NULLIF(TRIM(COALESCE(p_colegio_proc, '')), ''),
            NULLIF(TRIM(COALESCE(p_rude, '')), ''),
            NULLIF(TRIM(COALESCE(p_provincia, '')), ''),
            NULLIF(TRIM(COALESCE(p_ciudad, '')), ''),
            COALESCE(NULLIF(TRIM(COALESCE(p_pais, '')), ''), 'Bolivia'),
            p_fnacimiento,
            NULLIF(TRIM(COALESCE(p_emergencia, '')), ''),
            p_estado_reg, p_statusGeneral, p_foto, p_folio,
            NULLIF(TRIM(COALESCE(p_estante, '')), ''),
            NULLIF(TRIM(COALESCE(p_gaveta, '')), ''),
            NULLIF(TRIM(COALESCE(p_estado_legajo, '')), ''));
    COMMIT;
    SELECT 'Estudiante registrado exitosamente.' AS mensaje;
END$$

DROP PROCEDURE IF EXISTS `updateEstudiante`$$
CREATE PROCEDURE `updateEstudiante`(
    IN p_idEstudiante BIGINT, IN p_ci VARCHAR(30), IN p_rude VARCHAR(50), IN p_estadoReg VARCHAR(20),
    IN p_nombre VARCHAR(80), IN p_apellido VARCHAR(100), IN p_sexo VARCHAR(10), IN p_telefono VARCHAR(15),
    IN p_email VARCHAR(100), IN p_direccion VARCHAR(100), IN p_fnacimiento DATE, IN p_pais VARCHAR(50),
    IN p_ciudad VARCHAR(80), IN p_provincia VARCHAR(80), IN p_colegioProc VARCHAR(250),
    IN p_emergencia VARCHAR(100), IN p_tipoId BIGINT, IN p_password TEXT, IN p_usuario TEXT, IN p_status INT,
    IN p_foto VARCHAR(255),
    IN p_folio INT UNSIGNED, IN p_estante VARCHAR(10), IN p_gaveta VARCHAR(20), IN p_estado_legajo VARCHAR(20)
)
BEGIN
    DECLARE v_idPersona BIGINT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    SELECT id_persona INTO v_idPersona FROM estudiante WHERE id_estudiante = p_idEstudiante;
    IF v_idPersona IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Estudiante no encontrado.';
    END IF;
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci AND id_persona != v_idPersona) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado en otro estudiante.';
    END IF;
    IF (p_telefono IS NOT NULL AND TRIM(COALESCE(p_telefono, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE cel = p_telefono AND id_persona != v_idPersona) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El celular ya está registrado en otro usuario.';
        END IF;
    END IF;
    IF (p_email IS NOT NULL AND TRIM(COALESCE(p_email, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE email = p_email AND id_persona != v_idPersona) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El email ya está registrado en otro usuario.';
        END IF;
    END IF;
    IF (p_rude IS NOT NULL AND TRIM(COALESCE(p_rude, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE rude = p_rude AND id_estudiante != p_idEstudiante) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado en otro estudiante.';
        END IF;
    END IF;
    IF (p_folio IS NOT NULL) THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE folio_fisico = p_folio AND id_estudiante != p_idEstudiante) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El folio ya está asignado a otro estudiante.';
        END IF;
    END IF;
    UPDATE persona SET ci=p_ci, nombre=p_nombre, apellido=p_apellido, sexo=p_sexo,
        cel=NULLIF(TRIM(COALESCE(p_telefono, '')), ''),
        email=NULLIF(TRIM(COALESCE(p_email, '')), ''),
        direccion_dom=NULLIF(TRIM(COALESCE(p_direccion, '')), ''),
        id_rol=p_tipoId, password=p_password, usuario=p_usuario
    WHERE id_persona = v_idPersona;
    UPDATE estudiante SET
        rude=NULLIF(TRIM(COALESCE(p_rude, '')), ''), estado_reg=p_estadoReg,
        colegio_proc=NULLIF(TRIM(COALESCE(p_colegioProc, '')), ''),
        provincia=NULLIF(TRIM(COALESCE(p_provincia, '')), ''),
        ciudad=NULLIF(TRIM(COALESCE(p_ciudad, '')), ''),
        pais=COALESCE(NULLIF(TRIM(COALESCE(p_pais, '')), ''), 'Bolivia'),
        fnacimiento=p_fnacimiento, status=p_status,
        emergencia=NULLIF(TRIM(COALESCE(p_emergencia, '')), ''),
        foto=p_foto, folio_fisico=p_folio,
        estante=NULLIF(TRIM(COALESCE(p_estante, '')), ''),
        gaveta=NULLIF(TRIM(COALESCE(p_gaveta, '')), ''),
        estado_legajo=NULLIF(TRIM(COALESCE(p_estado_legajo, '')), '')
    WHERE id_estudiante = p_idEstudiante;
    COMMIT;
    SELECT 'Estudiante actualizado correctamente.' AS mensaje;
END$$

-- 4) matricular_estudiante_y_generar_pensiones: cuerpo VIVO (con mes_num y
-- fecha_vencimiento) + EXIT HANDLER. Sin el handler, un duplicado/SIGNAL dejaba
-- la transacción abierta y envenenaba el resto del request (I2-hallazgo).
DROP PROCEDURE IF EXISTS `matricular_estudiante_y_generar_pensiones`$$
CREATE PROCEDURE `matricular_estudiante_y_generar_pensiones`(
    IN p_ci VARCHAR(30), IN p_gestion INT, IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20), IN p_folio TEXT, IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_monto_pension DECIMAL(10, 2);
    DECLARE v_id_estudiante BIGINT;
    DECLARE v_id_matricula BIGINT;
    DECLARE v_mes VARCHAR(10);
    DECLARE v_mes_index INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
    START TRANSACTION;
    IF NOT EXISTS (SELECT 1 FROM estudiante e JOIN persona p ON p.id_persona = e.id_persona WHERE p.ci = p_ci AND e.status != 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estudiante no existe.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM estudiante e JOIN persona p ON p.id_persona = e.id_persona WHERE p.ci = p_ci AND e.status = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estudiante no está activo: no puede matricularse ni generar pensiones.';
    END IF;
    SELECT e.id_estudiante INTO v_id_estudiante
    FROM estudiante e JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;
    IF EXISTS (SELECT 1 FROM matricula WHERE id_estudiante = v_id_estudiante AND gestion = p_gestion) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estudiante ya está matriculado.';
    END IF;
    INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
    VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);
    SET v_id_matricula = LAST_INSERT_ID();
    SELECT monto_pension INTO v_monto_pension FROM gestion WHERE gestion = p_gestion LIMIT 1;
    IF v_monto_pension IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La gestión no existe.';
    END IF;
    SET v_mes_index = 2;
    WHILE v_mes_index <= 11 DO
        CASE v_mes_index
            WHEN 2 THEN SET v_mes = 'Febrero';
            WHEN 3 THEN SET v_mes = 'Marzo';
            WHEN 4 THEN SET v_mes = 'Abril';
            WHEN 5 THEN SET v_mes = 'Mayo';
            WHEN 6 THEN SET v_mes = 'Junio';
            WHEN 7 THEN SET v_mes = 'Julio';
            WHEN 8 THEN SET v_mes = 'Agosto';
            WHEN 9 THEN SET v_mes = 'Septiembre';
            WHEN 10 THEN SET v_mes = 'Octubre';
            WHEN 11 THEN SET v_mes = 'Noviembre';
        END CASE;
        INSERT INTO pensiones (id_matricula, monto, estado_pago, status, mes, mes_num, fecha_vencimiento)
        VALUES (v_id_matricula, v_monto_pension, 0, 1, v_mes, v_mes_index, LAST_DAY(CONCAT(p_gestion,'-',v_mes_index,'-01')));
        SET v_mes_index = v_mes_index + 1;
    END WHILE;
    COMMIT;
END$$

DELIMITER ;

-- 3) Verificación post-migración --
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
--  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matricula'
--  AND COLUMN_NAME = 'motivo_estado';
