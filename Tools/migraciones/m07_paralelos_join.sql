-- =====================================================
-- MIGRACIÓN M07 — Fix JOIN en SPs de paralelos por gestión
-- El filtro `m.gestion = p_gestion` estaba en el WHERE sobre un LEFT JOIN:
-- los paralelos con matrículas SOLO de otras gestiones desaparecían
-- (lista vacía al rematricular a un año sin matrículas, ej. 2027).
-- Se mueve la condición al ON. COUNT sin cambios (mismo criterio que antes).
-- Sin cambios de tablas. Requiere SPs base del script V2.
-- =====================================================
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_contar_inscritos_paralelo_por_gestion`$$
CREATE PROCEDURE `sp_contar_inscritos_paralelo_por_gestion`(IN p_gestion BIGINT)
BEGIN
    SELECT p.id_paralelo, p.nivel, p.grado, p.sigla, p.cupo,
           p.fecha_reg, p.tutor, p.turno, p.status,
           COUNT(m.id_estudiante) AS total_inscritos
    FROM paralelo p
    LEFT JOIN matricula m ON p.id_paralelo = m.id_paralelo AND m.gestion = p_gestion
    GROUP BY p.id_paralelo
    ORDER BY p.nivel, p.grado, p.sigla;
END$$

DROP PROCEDURE IF EXISTS `paralelo_por_gestion`$$
CREATE PROCEDURE `paralelo_por_gestion`(
    IN p_paralelo BIGINT,
    IN p_gestion BIGINT
)
BEGIN
    SELECT p.id_paralelo, p.nivel, p.grado, p.sigla,
           p.cupo, p.fecha_reg, p.tutor, p.turno, p.status,
           COUNT(m.id_estudiante) AS total_inscritos
    FROM paralelo p
    LEFT JOIN matricula m ON p.id_paralelo = m.id_paralelo AND m.gestion = p_gestion
    WHERE p.id_paralelo = p_paralelo
    GROUP BY p.id_paralelo;
END$$

DELIMITER ;
