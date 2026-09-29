-- =====================================================
-- MIGRACIÓN M06 — Trazabilidad de edición + entrega documental
-- Tipos: cambio_curso | correccion | entrega_docs | cambio_estado |
--        rectificacion | docs_pendientes | creacion
-- Detalle JSON: {antes, despues, docs, entregado_por, motivo...}
-- MySQL 9.1: sin IF NOT EXISTS en índices (aplicar con chequeo).
-- =====================================================
CREATE TABLE IF NOT EXISTS `matricula_cambios` (
    `id_cambio` BIGINT NOT NULL AUTO_INCREMENT,
    `id_matricula` BIGINT NOT NULL,
    `tipo` VARCHAR(25) NOT NULL,
    `detalle` TEXT NULL DEFAULT NULL,
    `id_usuario` BIGINT NULL DEFAULT NULL,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_cambio`),
    KEY `idx_mc_matricula` (`id_matricula`),
    CONSTRAINT `mc_ibfk_1` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
