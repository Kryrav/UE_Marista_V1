-- =====================================================
-- MIGRACIÓN M03 — ITERACIÓN 3: inclusión, auditoría, índices
-- Sistema Marista SS.CC. — Módulo Estudiantes / Matrícula
-- Objetivo N-03/N-04, D-04, S-03, F-07, U-05:
--  - Tabla estudiante_inclusion (discapacidad/apoyo, paralela, comisión).
--    Separada para NO tocar los SPs de alta/edición (compat total).
--  - Auditoría mínima: created_by/updated_by/updated_at en
--    estudiante y matricula (quién creó/modificó, S-03/D-04).
--  - Índice de búsqueda por apellidos (F-07).
-- MySQL 9.1: sin IF NOT EXISTS en columnas/índices; aplicar con el
-- script de chequeo (ver Docs/ITERACION_3). Re-aplicar: ignorar 1060/1061.
-- =====================================================

-- 1) Inclusión y apoyo (1-1 con estudiante) --
CREATE TABLE IF NOT EXISTS `estudiante_inclusion` (
    `id_estudiante` BIGINT NOT NULL,
    `tiene_discapacidad` TINYINT(1) NOT NULL DEFAULT 0,
    `tipo_discapacidad` VARCHAR(100) NULL DEFAULT NULL,
    `adaptaciones` TEXT NULL DEFAULT NULL,
    `centro_especial` VARCHAR(150) NULL DEFAULT NULL,
    `matricula_paralela` TINYINT(1) NOT NULL DEFAULT 0,
    `requiere_comision` TINYINT(1) NOT NULL DEFAULT 0,
    `created_by` BIGINT NULL DEFAULT NULL,
    `updated_by` BIGINT NULL DEFAULT NULL,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_estudiante`),
    CONSTRAINT `estinc_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Auditoría estudiante --
ALTER TABLE `estudiante`
    ADD COLUMN `created_by` BIGINT NULL DEFAULT NULL,
    ADD COLUMN `updated_by` BIGINT NULL DEFAULT NULL,
    ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- 3) Auditoría matrícula (+ id_user se corrige por código, el SP lo deja en 1) --
ALTER TABLE `matricula`
    ADD COLUMN `created_by` BIGINT NULL DEFAULT NULL,
    ADD COLUMN `updated_by` BIGINT NULL DEFAULT NULL,
    ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- 4) Índice de búsqueda por apellidos --
-- CREATE INDEX `idx_persona_apellido` ON `persona` (`apellido`);
