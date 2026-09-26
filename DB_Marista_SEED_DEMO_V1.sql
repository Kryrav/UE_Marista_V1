-- =====================================================================
-- SEED DEMO V1 — UE Marista — datos 100% inventados para desarrolladores
-- Archivo: DB_Marista_SEED_DEMO_V1.sql
-- Uso (en orden):
--   1) mysql -u root db_marista_demo < DB_Marista_Script_V2.sql   (estructura base)
--   2) mysql -u root db_marista_demo < Tools/migraciones/m01_documentacion_pendiente.sql
--      (si MySQL 8/9 rechaza IF NOT EXISTS, aplicar columnas con chequeo previo;
--       ver Docs/ITERACION_1_Pasos_M01.md)
--   3) mysql -u root db_marista_demo < DB_Marista_SEED_DEMO_V1.sql  (este archivo)
-- Requiere SPs: sp_registrar_estudiante (24p, M01 NULL-aware),
--   matricular_estudiante_y_generar_pensiones, listar_matriculas.
-- Claves demo: TODOS los usuarios usan clave "marista123".
-- Nombres, CI, RUDE, emails y teléfonos son ficticios (serie 10000001... / 20000001...).
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Limpieza idempotente (solo demo; en BD con datos reales NO ejecutar sin backup) --
DELETE FROM `pensiones`;
DELETE FROM `matricula`;
DELETE FROM `padre`;
DELETE FROM `estudiante`;
DELETE FROM `persona`;
DELETE FROM `permisos`;
DELETE FROM `paralelo`;
DELETE FROM `gestion`;
DELETE FROM `materia`;
DELETE FROM `cobro`;
DELETE FROM `colegio`;
DELETE FROM `modulo` WHERE `idmodulo` >= 1;
DELETE FROM `rol` WHERE `idrol` >= 1;
ALTER TABLE `persona` AUTO_INCREMENT = 1;

-- Roles (incluye 14 Tutor usado por el código) --
INSERT INTO `rol` (`idrol`,`nombrerol`,`descripcion`,`status`) VALUES
(1,'Administrador','Acceso total al sistema',1),
(2,'Director','Supervisión general',1),
(3,'Coordinador','Coordinación académica',1),
(4,'Secretario','Gestión administrativa y matrículas',1),
(5,'Docente','Personal docente',1),
(6,'Contador','Gestión financiera',1),
(7,'Estudiante','Alumnos del colegio',1),
(14,'Tutor','Padres y apoderados',1);

-- Módulos --
INSERT INTO `modulo` (`idmodulo`,`titulo`,`descripcion`,`status`) VALUES
(1,'Dashboard','Panel principal',1),
(2,'Usuarios','Gestión de usuarios',1),
(3,'Estudiantes','Gestión de estudiantes',1),
(4,'Matricula','Proceso de matriculación',1),
(5,'Pagos Mensualidad','Gestión de pagos',1),
(6,'Cobros','Configuración de cobros',1),
(7,'Cursos','Gestión de cursos y paralelos',1),
(8,'Docentes','Gestión de docentes',1),
(9,'Administrativos','Personal administrativo',1),
(10,'Materias','Gestión de materias',1),
(11,'Lectivo','Año lectivo',1),
(12,'Tutores','Gestión de padres y tutores',1);

-- Permisos demo (admin todo; resto operativo mínimo) --
INSERT INTO `permisos` (`rolid`,`moduloid`,`r`,`w`,`u`,`d`) VALUES
(1,1,1,1,1,1),(1,2,1,1,1,1),(1,3,1,1,1,1),(1,4,1,1,1,1),(1,5,1,1,1,1),(1,6,1,1,1,1),
(1,7,1,1,1,1),(1,8,1,1,1,1),(1,9,1,1,1,1),(1,10,1,1,1,1),(1,11,1,1,1,1),(1,12,1,1,1,1),
(2,1,1,0,0,0),(2,2,1,0,0,0),(2,3,1,0,0,0),(2,4,1,0,0,0),(2,5,1,0,0,0),(2,6,1,0,0,0),
(2,7,1,0,0,0),(2,8,1,0,0,0),(2,9,1,0,0,0),(2,10,1,0,0,0),(2,11,1,0,0,0),(2,12,1,0,0,0),
(4,1,1,0,0,0),(4,3,1,1,1,1),(4,4,1,1,1,1),(4,7,1,0,0,0),(4,12,1,1,1,1),
(5,1,1,0,0,0),(5,3,1,0,0,0),(5,7,1,0,0,0),
(6,1,1,0,0,0),(6,5,1,1,1,1),(6,6,1,1,1,1),
(7,1,1,0,0,0),
(14,1,1,0,0,0);

-- Colegio demo --
INSERT INTO `colegio` (`nombre_col`,`codigo_registro_col`,`tipo_col`,`nivel_educativo_col`,`direccion_col`,`ciudad_col`,`telefono_col`,`correo_col`,`sitio_web_col`,`director_col`,`fecha_creacion_col`,`nro_estudiantes_col`,`nro_docentes_col`,`descripcion_col`,`horario_atencion_col`,`status`) VALUES
('Colegio Demo Marista','DEMO-0001','Convenio','Primaria','Av. Demo 123, Zona Central','Roboré','70000001','demo@marista.bo','www.demo.bo','Directora Demo','2000-03-10',120,12,'Colegio ficticio para pruebas de desarrollo.','Lun-Vie 7:00-12:00',1);

-- Gestiones --
INSERT INTO `gestion` (`gestion`,`inicio`,`fin`,`gestion_l`,`monto_pension`,`descripcion`,`status`) VALUES
(2025,'2025-02-03','2025-11-30','2025',25.00,'Gestión 2025 cerrada (demo)',0),
(2026,'2026-02-02','2026-11-30','2026',25.00,'Gestión 2026 activa (demo)',1);

-- Paralelos demo (tutores con nombres ficticios) --
INSERT INTO `paralelo` (`tutor`,`nivel`,`grado`,`sigla`,`cupo`,`turno`,`status`) VALUES
('Docente Demo Uno','Inicial',0,'A',30,'Mañana',1),
('Docente Demo Uno','Primaria',1,'A',30,'Mañana',1),
('Docente Demo Dos','Primaria',2,'A',30,'Mañana',1),
('Docente Demo Tres','Primaria',3,'A',30,'Mañana',1),
('Docente Demo Cuatro','Primaria',4,'A',30,'Mañana',1),
('Docente Demo Cinco','Primaria',5,'A',30,'Mañana',1);

-- Materias demo --
INSERT INTO `materia` (`area_mat`,`nombre_mat`,`descripcion_mat`,`grado`,`nivel`,`horas_mat`,`status`) VALUES
('Lenguaje','Lenguaje y Comunicación','Demo lenguaje','1','Primaria',4,1),
('Matemática','Matemática','Demo matemática','1','Primaria',4,1),
('Ciencias','Ciencias Naturales','Demo ciencias','2','Primaria',3,1),
('Valores','Valores y Espiritualidad','Demo valores','1','Primaria',2,1);

-- Cobro demo --
INSERT INTO `cobro` (`nombre`,`descripcion`,`tipo`,`ncuota`,`valor`,`status`) VALUES
('Cuota refacción demo','Cuota extra ficticia para pruebas','Unico',1,50.00,1);

-- Usuarios demo (clave de TODOS: marista123; hash bcrypt) --
-- hash = password_hash('marista123'): $2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni
INSERT INTO `persona` (`ci`,`nombre`,`apellido`,`sexo`,`direccion_dom`,`cel`,`email`,`usuario`,`password`,`id_rol`,`status`) VALUES
('10000001','Admin','Demo','M','Av. Demo 1','70000001','admin@demo.bo','admin','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',1,1),
('10000002','Directora','Demo','F','Av. Demo 2','70000002','direccion@demo.bo','direccion','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',2,1),
('10000003','Secretaria','Demo','F','Av. Demo 3','70000003','secretaria@demo.bo','secretaria','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',4,1),
('10000004','Docente','Demo Uno','M','Av. Demo 4','70000004','docente@demo.bo','docente','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',5,1),
('10000005','Contador','Demo','M','Av. Demo 5','70000005','contador@demo.bo','contador','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',6,1);

SET FOREIGN_KEY_CHECKS = 1;

-- Estudiantes demo vía SP real (24p M01). 2 completos + 2 pendientes (M01) --
-- DEMO-001 completo
CALL sp_registrar_estudiante('20000001','Ana','Demo Uno','F','Av. Demo 11','71000001','ana.demo@demo.bo','ana.demo@demo.bo','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',7,'Colegio Anterior Demo','RUDE-DEMO-001','Roboré','Roboré','Bolivia','2016-04-10','Av. Demo 11','Nuevo',1,NULL,101,NULL,NULL,NULL);
-- DEMO-002 completo
CALL sp_registrar_estudiante('20000002','Luis','Demo Dos','M','Av. Demo 12','71000002','luis.demo@demo.bo','luis.demo@demo.bo','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',7,'Colegio Anterior Demo','RUDE-DEMO-002','Roboré','Roboré','Bolivia','2015-08-20','Av. Demo 12','Antiguo',1,NULL,102,NULL,NULL,'archivado');
-- DEMO-003 pendiente (SIN rude/email/cel -> M01 lo permite, usuario=CI)
CALL sp_registrar_estudiante('20000003','María','Demo Tres','F','Av. Demo 13',NULL,NULL,'20000003','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',7,NULL,NULL,NULL,NULL,'Bolivia','2017-02-15',NULL,'Nuevo',1,NULL,103,NULL,NULL,NULL);
-- DEMO-004 pendiente parcial (con RUDE, sin email)
CALL sp_registrar_estudiante('20000004','Pedro','Demo Cuatro','M','Av. Demo 14',NULL,NULL,'20000004','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',7,NULL,'RUDE-DEMO-004',NULL,NULL,'Bolivia','2016-11-05',NULL,'Nuevo',1,NULL,104,NULL,NULL,NULL);

-- Tutor demo para DEMO-001 (madre ficticia, rol 14) --
INSERT INTO `persona` (`ci`,`nombre`,`apellido`,`sexo`,`direccion_dom`,`cel`,`email`,`usuario`,`password`,`id_rol`,`status`) VALUES
('30000001','Madre','Demo Uno','F','Av. Demo 11','72000001','madre.demo@demo.bo','madre.demo@demo.bo','$2y$10$yB0LJbx/nm/omnUpf7setuPpbgGbFBzX9b9cfOVTAmBG9QLmhlxni',14,1);
INSERT INTO `padre` (`id_persona`,`id_estudiante`,`tipo_parentesco`,`nacionalidad`,`estado_civil`,`profesion`,`empresa_trabajo`,`status`,`observaciones`)
SELECT (SELECT id_persona FROM persona WHERE ci='30000001'), (SELECT e.id_estudiante FROM estudiante e JOIN persona p ON e.id_persona=p.id_persona WHERE p.ci='20000001'),
'Madre','Boliviana','Casada','Comerciante','Mercado Demo',1,'Apoderada demo';

-- Matrículas 2026 (generan 10 pensiones Feb-Nov c/u vía SP) --
-- IDs de paralelo por lookup (no hardcodeados: el AUTO_INCREMENT varía) --
CALL matricular_estudiante_y_generar_pensiones('20000001',2026,(SELECT id_paralelo FROM paralelo WHERE nivel='Primaria' AND grado=1 AND sigla='A' LIMIT 1),'Regular','','Confirmado');
CALL matricular_estudiante_y_generar_pensiones('20000002',2026,(SELECT id_paralelo FROM paralelo WHERE nivel='Primaria' AND grado=2 AND sigla='A' LIMIT 1),'Regular','','Inscrito');
CALL matricular_estudiante_y_generar_pensiones('20000003',2026,(SELECT id_paralelo FROM paralelo WHERE nivel='Primaria' AND grado=1 AND sigla='A' LIMIT 1),'Regular','','Pendiente_Documentos');
CALL matricular_estudiante_y_generar_pensiones('20000004',2026,(SELECT id_paralelo FROM paralelo WHERE nivel='Primaria' AND grado=1 AND sigla='A' LIMIT 1),'Becado','','Confirmado');

-- Documentación pendiente de DEMO-003 (M01: plazo + checklist + compromiso) --
UPDATE `matricula` m
JOIN `estudiante` e ON m.id_estudiante = e.id_estudiante
JOIN `persona` p ON e.id_persona = p.id_persona
SET m.`plazo_documentos_hasta` = DATE_ADD(CURDATE(), INTERVAL 42 DAY),
    m.`docs_checklist` = '{"ci":1,"cert_nac":0,"rude":0,"solicitud":1}',
    m.`compromiso_firmado` = 1,
    m.`docs_observacion` = 'Demo M01: falta RUDE y cert. nacimiento'
WHERE p.ci = '20000003' AND m.gestion = 2026;

-- Verificación rápida (el instalador debe ver 4/4/40): --
-- SELECT (SELECT COUNT(*) FROM persona) AS personas, (SELECT COUNT(*) FROM estudiante) AS estudiantes, (SELECT COUNT(*) FROM matricula WHERE gestion=2026) AS matriculas_2026, (SELECT COUNT(*) FROM pensiones) AS pensiones;
