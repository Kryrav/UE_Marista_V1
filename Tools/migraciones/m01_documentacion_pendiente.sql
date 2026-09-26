-- =====================================================
-- MIGRACIÓN M01 — ITERACIÓN 1: Documentación diferida 30 días hábiles
-- Sistema Marista SS.CC. — Módulo Estudiantes / Matrícula
-- Objetivo N-01/N-07, F-06, U-04:
--  - Permitir inscripción sin documentación completa
--  - Estado Pendiente_Documentos + plazo + checklist + compromiso
--  - Flexibilizar RUDE/email/celular como diferibles (NULL)
-- Compatibilidad: solo ADD NULL / MODIFY a NULL, sin borrar datos.
-- Aplicar en staging primero. Requiere MariaDB/MySQL 8.
-- =====================================================

-- 1) Matrícula: columnas documentales (todas NULLables) --------
-- NOTA MySQL 9.1: no acepta ADD COLUMN IF NOT EXISTS (solo MariaDB).
-- Aplicado el 2026-09-26 con chequeo previo INFORMATION_SCHEMA (ver
-- Backups/M01_2026-09-26_Documentacion-Pendiente/MIGRACION_APLICADA.txt).
-- Para re-aplicar: ignorar error 1060 (columna duplicada) o usar el
-- script apply con chequeo. En MariaDB puede usarse IF NOT EXISTS.
ALTER TABLE `matricula`
    ADD COLUMN `plazo_documentos_hasta` DATE NULL DEFAULT NULL,
    ADD COLUMN `docs_checklist` TEXT NULL DEFAULT NULL,
    ADD COLUMN `compromiso_firmado` TINYINT(1) NULL DEFAULT 0,
    ADD COLUMN `docs_observacion` VARCHAR(255) NULL DEFAULT NULL;

-- Índice para alertas de vencimiento (MySQL no soporta IF NOT EXISTS en índices:
-- crear solo si falta; ignorar error 1061 si ya existe)
-- CREATE INDEX `idx_matricula_plazo` ON `matricula` (`plazo_documentos_hasta`, `estado_inscripcion`);

-- 2) Flexibilizar diferibles: RUDE / email / celular / direcciones --
-- (usuario se mantiene NOT NULL: si no hay email se usa CI como usuario)
ALTER TABLE `persona`
    MODIFY `cel` VARCHAR(15) NULL DEFAULT NULL,
    MODIFY `email` VARCHAR(100) NULL DEFAULT NULL,
    MODIFY `direccion_dom` VARCHAR(100) NULL DEFAULT NULL;

ALTER TABLE `estudiante`
    MODIFY `rude` VARCHAR(50) NULL DEFAULT NULL,
    MODIFY `colegio_proc` VARCHAR(250) NULL DEFAULT NULL,
    MODIFY `provincia` VARCHAR(80) NULL DEFAULT NULL,
    MODIFY `ciudad` VARCHAR(80) NULL DEFAULT NULL,
    MODIFY `pais` VARCHAR(50) NULL DEFAULT 'Bolivia';

-- Legajo (por si la instancia aún no tiene la migración 2026-04; en MySQL sin IF NOT EXISTS: ignorar 1060)
ALTER TABLE `estudiante`
    ADD COLUMN `folio_fisico` INT UNSIGNED NULL DEFAULT NULL,
    ADD COLUMN `estante` VARCHAR(10) NULL DEFAULT NULL,
    ADD COLUMN `gaveta` VARCHAR(20) NULL DEFAULT NULL,
    ADD COLUMN `estado_legajo` VARCHAR(20) NULL DEFAULT NULL,
    ADD COLUMN `foto` VARCHAR(255) NULL DEFAULT NULL;

DELIMITER $$

-- 3) SP alta: 24 params (igual firma que usa EstudiantesModel) + --
--    unicidad NULL-aware: solo valida cel/email/rude si vienen con valor --
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
    START TRANSACTION;
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado.';
    END IF;
    -- Diferibles: solo se exige unicidad si traen valor no vacío
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
    -- Si no hay email, el usuario de acceso es el CI (login por CI o email)
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
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado.';
        END IF;
    END IF;
    IF (p_folio IS NOT NULL) THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE folio_fisico = p_folio) THEN
            ROLLBACK;
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

-- 4) SP edición: 25 params (igual firma que usa el Modelo) + NULL-aware --
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
    START TRANSACTION;
    SELECT id_persona INTO v_idPersona FROM estudiante WHERE id_estudiante = p_idEstudiante;
    IF v_idPersona IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Estudiante no encontrado.';
    END IF;
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci AND id_persona != v_idPersona) THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado en otro estudiante.';
    END IF;
    IF (p_telefono IS NOT NULL AND TRIM(COALESCE(p_telefono, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE cel = p_telefono AND id_persona != v_idPersona) THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El celular ya está registrado en otro usuario.';
        END IF;
    END IF;
    IF (p_email IS NOT NULL AND TRIM(COALESCE(p_email, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM persona WHERE email = p_email AND id_persona != v_idPersona) THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El email ya está registrado en otro usuario.';
        END IF;
    END IF;
    IF (p_rude IS NOT NULL AND TRIM(COALESCE(p_rude, '')) != '') THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE rude = p_rude AND id_estudiante != p_idEstudiante) THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado en otro estudiante.';
        END IF;
    END IF;
    IF (p_folio IS NOT NULL) THEN
        IF EXISTS (SELECT 1 FROM estudiante WHERE folio_fisico = p_folio AND id_estudiante != p_idEstudiante) THEN
            ROLLBACK;
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

-- 5) Reporte de matrículas: exponer columnas documentales (compat: si no existen, SP viejo sigue) --
DROP PROCEDURE IF EXISTS `listar_matriculas`$$
CREATE PROCEDURE `listar_matriculas`()
BEGIN
    SELECT m.id_matricula, m.gestion, m.tipo AS tipo_matricula, m.folio,
           m.estado_inscripcion, m.status AS estado_matricula,
           m.plazo_documentos_hasta, m.docs_checklist, m.compromiso_firmado, m.docs_observacion,
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

DELIMITER ;

-- 5) Verificación post-migración (lectura, no escribe) --
-- SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
--  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('persona','estudiante','matricula')
--  AND COLUMN_NAME IN ('cel','email','rude','plazo_documentos_hasta','docs_checklist','compromiso_firmado');
