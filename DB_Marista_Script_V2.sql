-- =====================================================
-- SISTEMA DE GESTIÓN EDUCATIVA MARISTA SS.CC.
-- Base de Datos: db_marista (canónica, minúsculas para Linux)
-- Versión: 2.0 (Corregida y Optimizada)
-- Codificación: utf8mb4
-- =====================================================

-- Eliminar base de datos si existe (¡CUIDADO!)
DROP DATABASE IF EXISTS db_marista;

-- Crear base de datos con charset correcto
CREATE DATABASE db_marista 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE db_marista;

-- =====================================================
-- TABLAS BASE (Sin dependencias)
-- =====================================================

-- -----------------------------------------------------
-- Table `rol`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rol`;
CREATE TABLE `rol` (
    `idrol` BIGINT NOT NULL AUTO_INCREMENT,
    `nombrerol` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`idrol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `cargo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cargo`;
CREATE TABLE `cargo` (
    `id_cargo` BIGINT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `status` TINYINT(1) NOT NULL DEFAULT '1',
    `salario_base` DECIMAL(10,2) DEFAULT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `colegio`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `colegio`;
CREATE TABLE `colegio` (
    `id_colegio` BIGINT NOT NULL AUTO_INCREMENT,
    `nombre_col` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `codigo_registro_col` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tipo_col` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nivel_educativo_col` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `direccion_col` VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ciudad_col` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `telefono_col` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `correo_col` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `sitio_web_col` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `director_col` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_creacion_col` DATE DEFAULT NULL,
    `nro_estudiantes_col` INT DEFAULT NULL,
    `nro_docentes_col` INT DEFAULT NULL,
    `descripcion_col` TEXT COLLATE utf8mb4_unicode_ci,
    `horario_atencion_col` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_colegio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `configuracion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE `configuracion` (
    `id_config` BIGINT NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `telefono` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `direccion` TEXT COLLATE utf8mb4_unicode_ci,
    `valor` TEXT COLLATE utf8mb4_unicode_ci,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `gestion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `gestion`;
CREATE TABLE `gestion` (
    `gestion` BIGINT NOT NULL,
    `inicio` DATE NOT NULL,
    `fin` DATE NOT NULL,
    `gestion_l` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `monto_pension` DECIMAL(10,2) DEFAULT '36.00',
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `status` INT DEFAULT '1',
    PRIMARY KEY (`gestion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `login_intentos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `login_intentos`;
CREATE TABLE `login_intentos` (
    `id_intento` BIGINT NOT NULL AUTO_INCREMENT,
    `usuario` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `exitoso` TINYINT(1) NOT NULL DEFAULT '0',
    `ip_address` VARCHAR(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_agent` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_intento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_intento`),
    KEY `idx_usuario` (`usuario`),
    KEY `idx_fecha` (`fecha_intento`),
    KEY `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `modulo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `modulo`;
CREATE TABLE `modulo` (
    `idmodulo` BIGINT NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`idmodulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `persona` (Núcleo del sistema)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `persona`;
CREATE TABLE `persona` (
    `id_persona` BIGINT NOT NULL AUTO_INCREMENT,
    `ci` VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nombre` VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL,
    `apellido` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `sexo` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `direccion_dom` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cel` VARCHAR(15) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `usuario` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` TEXT COLLATE utf8mb4_bin NOT NULL,  -- bin para hashes
    `status` INT DEFAULT '2',
    `token` TEXT COLLATE utf8mb4_bin,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id_rol` BIGINT NOT NULL,
    PRIMARY KEY (`id_persona`),
    UNIQUE KEY `ci` (`ci`),
    UNIQUE KEY `cel` (`cel`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `usuario` (`usuario`),
    KEY `id_rol` (`id_rol`),
    KEY `idx_persona_nombre_apellido` (`nombre`,`apellido`),
    CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`idrol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `estudiante`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `estudiante`;
CREATE TABLE `estudiante` (
    `id_estudiante` BIGINT NOT NULL AUTO_INCREMENT,
    `id_persona` BIGINT NOT NULL,
    `colegio_proc` VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL,
    `rude` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `provincia` VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ciudad` VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL,
    `pais` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fnacimiento` DATE NOT NULL,
    `emergencia` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `estado_reg` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INT DEFAULT '1',
    PRIMARY KEY (`id_estudiante`),
    UNIQUE KEY `unique_rude` (`rude`),
    KEY `id_persona` (`id_persona`),
    CONSTRAINT `estudiante_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `paralelo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `paralelo`;
CREATE TABLE `paralelo` (
    `id_paralelo` BIGINT NOT NULL AUTO_INCREMENT,
    `tutor` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT 'Sin asignación',
    `nivel` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `grado` INT NOT NULL,
    `sigla` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cupo` INT DEFAULT '30',
    `turno` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Mañana',
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INT DEFAULT '1',
    PRIMARY KEY (`id_paralelo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `materia`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `materia`;
CREATE TABLE `materia` (
    `id_materia` BIGINT NOT NULL AUTO_INCREMENT,
    `area_mat` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nombre_mat` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `descripcion_mat` TEXT COLLATE utf8mb4_unicode_ci,
    `grado` INT DEFAULT NULL,
    `nivel` VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `horas_mat` INT DEFAULT NULL,
    `status` INT DEFAULT '1',
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_materia`),
    UNIQUE KEY `unique_materia_grado` (`nombre_mat`, `grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `docente`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `docente`;
CREATE TABLE `docente` (
    `id_docente` BIGINT NOT NULL AUTO_INCREMENT,
    `id_persona` BIGINT NOT NULL,
    `titulo_academico` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `especialidad` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `años_experiencia` INT NOT NULL,
    `nivel_educativo` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fecha_contratacion` DATE NOT NULL,
    `fecha_salida` DATE NOT NULL,
    `salario` DECIMAL(10,2) DEFAULT NULL,
    `nota` TEXT COLLATE utf8mb4_unicode_ci,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INT DEFAULT '1',
    PRIMARY KEY (`id_docente`),
    KEY `id_persona` (`id_persona`),
    CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `administrativo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `administrativo`;
CREATE TABLE `administrativo` (
    `id_admin` BIGINT NOT NULL AUTO_INCREMENT,
    `id_cargo` BIGINT NOT NULL,
    `id_persona` BIGINT NOT NULL,
    `fecha_ingreso` DATE NOT NULL,
    `fecha_retiro` DATE DEFAULT NULL,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INT DEFAULT '1',
    PRIMARY KEY (`id_admin`),
    KEY `id_cargo` (`id_cargo`),
    KEY `id_persona` (`id_persona`),
    CONSTRAINT `administrativo_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
    CONSTRAINT `administrativo_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `matricula`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `matricula`;
CREATE TABLE `matricula` (
    `id_matricula` BIGINT NOT NULL AUTO_INCREMENT,
    `id_estudiante` BIGINT DEFAULT NULL,
    `id_paralelo` BIGINT DEFAULT NULL,
    `id_user` BIGINT DEFAULT NULL,
    `gestion` BIGINT NOT NULL,
    `tipo` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `folio` TEXT COLLATE utf8mb4_unicode_ci,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `estado_inscripcion` VARCHAR(25) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_matricula`),
    KEY `id_estudiante` (`id_estudiante`),
    KEY `id_paralelo` (`id_paralelo`),
    KEY `gestion` (`gestion`),
    KEY `idx_matricula_estudiante_gestion` (`id_estudiante`,`gestion`),
    CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
    CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `paralelo` (`id_paralelo`),
    CONSTRAINT `matricula_ibfk_3` FOREIGN KEY (`gestion`) REFERENCES `gestion` (`gestion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `asignacion_docente`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `asignacion_docente`;
CREATE TABLE `asignacion_docente` (
    `id_asignacion` BIGINT NOT NULL AUTO_INCREMENT,
    `id_materia` BIGINT DEFAULT NULL,
    `id_docente` BIGINT DEFAULT NULL,
    `id_paralelo` BIGINT DEFAULT NULL,
    `anio_lectivo` YEAR NOT NULL,
    `horas_asignadas` INT NOT NULL,
    `status` TINYINT(1) DEFAULT '1',
    `observaciones` TEXT COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id_asignacion`),
    KEY `id_paralelo` (`id_paralelo`),
    KEY `id_materia` (`id_materia`),
    KEY `id_docente` (`id_docente`),
    CONSTRAINT `asignacion_docente_ibfk_1` FOREIGN KEY (`id_paralelo`) REFERENCES `paralelo` (`id_paralelo`),
    CONSTRAINT `asignacion_docente_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`),
    CONSTRAINT `asignacion_docente_ibfk_3` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `asignacion_materia`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `asignacion_materia`;
CREATE TABLE `asignacion_materia` (
    `id_asignacion` BIGINT NOT NULL AUTO_INCREMENT,
    `id_matricula` BIGINT NOT NULL,
    `id_materia` BIGINT NOT NULL,
    `fecha_asignacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `state` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_asignacion`),
    UNIQUE KEY `id_matricula` (`id_matricula`,`id_materia`),
    KEY `fk_asignacion_materia_materia` (`id_materia`),
    CONSTRAINT `fk_asignacion_materia_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_asignacion_materia_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `cobro`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cobro`;
CREATE TABLE `cobro` (
    `id_cobros` BIGINT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `tipo` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ncuota` INT NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_cobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `pensiones`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pensiones`;
CREATE TABLE `pensiones` (
    `id_pensiones` BIGINT NOT NULL AUTO_INCREMENT,
    `id_matricula` BIGINT DEFAULT NULL,
    `fecha_reg_pago` DATETIME DEFAULT NULL,
    `tipo_pago` ENUM('Efectivo','Transferencia','Deposito','Qr') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `codigo` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nombre` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `apellido` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ci` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `relacion` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `monto` DECIMAL(10,2) DEFAULT NULL,
    `estado_pago` TINYINT(1) DEFAULT '0',
    `status` TINYINT(1) DEFAULT '1',
    `mes` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pensiones`),
    UNIQUE KEY `id_matricula` (`id_matricula`,`mes`),
    KEY `idx_pensiones_matricula_mes` (`id_matricula`,`mes`),
    CONSTRAINT `pensiones_ibfk_1` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `padre`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `padre`;
CREATE TABLE `padre` (
    `id_padre` BIGINT NOT NULL AUTO_INCREMENT,
    `id_persona` BIGINT NOT NULL,
    `id_estudiante` BIGINT NOT NULL,
    `tipo_parentesco` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nacionalidad` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `estado_civil` VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `profesion` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `empresa_trabajo` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` INT DEFAULT '1',
    `fecha_reg` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `observaciones` TEXT COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id_padre`),
    KEY `id_persona` (`id_persona`),
    KEY `id_estudiante` (`id_estudiante`),
    CONSTRAINT `padre_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`),
    CONSTRAINT `padre_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `permisos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
    `idpermiso` BIGINT NOT NULL AUTO_INCREMENT,
    `rolid` BIGINT NOT NULL,
    `moduloid` BIGINT NOT NULL,
    `r` INT NOT NULL DEFAULT '0',
    `w` INT NOT NULL DEFAULT '0',
    `u` INT NOT NULL DEFAULT '0',
    `d` INT NOT NULL DEFAULT '0',
    PRIMARY KEY (`idpermiso`),
    KEY `rolid` (`rolid`),
    KEY `moduloid` (`moduloid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `eventos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE `eventos` (
    `id_evento` BIGINT NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `descripcion` TEXT COLLATE utf8mb4_unicode_ci,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE DEFAULT NULL,
    `lugar` VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_evento`),
    KEY `idx_fecha` (`fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `noticias`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `noticias`;
CREATE TABLE `noticias` (
    `id_noticia` BIGINT NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `contenido` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `imagen` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_publicacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_noticia`),
    KEY `idx_fecha` (`fecha_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `testimonios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testimonios`;
CREATE TABLE `testimonios` (
    `id_testimonio` BIGINT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `testimonio` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `cargo` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `foto` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id_testimonio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- -----------------------------------------------------
-- Insertar roles
-- -----------------------------------------------------
INSERT INTO `rol` (`idrol`, `nombrerol`, `descripcion`, `status`) VALUES
(1, 'Administrador', 'Acceso total al sistema', 1),
(2, 'Director', 'Supervisión general', 1),
(3, 'Coordinador', 'Coordinación académica', 1),
(4, 'Secretario', 'Gestión administrativa', 1),
(5, 'Docente', 'Personal docente', 1),
(6, 'Contador', 'Gestión financiera', 1),
(7, 'Estudiante', 'Alumnos del colegio', 1);

-- -----------------------------------------------------
-- Insertar módulos
-- -----------------------------------------------------
INSERT INTO `modulo` (`idmodulo`, `titulo`, `descripcion`, `status`) VALUES
(1, 'Dashboard', 'Panel principal', 1),
(2, 'Usuarios', 'Gestión de usuarios', 1),
(3, 'Estudiantes', 'Gestión de estudiantes', 1),
(4, 'Matricula', 'Proceso de matriculación', 1),
(5, 'Pagos Mensualidad', 'Gestión de pagos', 1),
(6, 'Cobros', 'Configuración de cobros', 1),
(7, 'Cursos', 'Gestión de cursos y paralelos', 1),
(8, 'Docentes', 'Gestión de docentes', 1),
(9, 'Administrativos', 'Personal administrativo', 1),
(10, 'Materias', 'Gestión de materias', 1),
(11, 'Lectivo', 'Año lectivo', 1);

-- -----------------------------------------------------
-- Insertar cargos
-- -----------------------------------------------------
INSERT INTO `cargo` (`id_cargo`, `nombre`, `descripcion`, `salario_base`) VALUES
(1, 'Director', 'Dirección general', 5000.00),
(2, 'Subdirector', 'Asistencia a dirección', 4000.00),
(3, 'Secretario', 'Tareas administrativas', 2500.00),
(4, 'Coordinador de Actividades', 'Actividades extracurriculares', 3500.00);

-- -----------------------------------------------------
-- Insertar colegio
-- -----------------------------------------------------
INSERT INTO `colegio` (
    `id_colegio`, `nombre_col`, `codigo_registro_col`, `tipo_col`, 
    `nivel_educativo_col`, `direccion_col`, `ciudad_col`, 
    `telefono_col`, `correo_col`, `sitio_web_col`, `director_col`, 
    `fecha_creacion_col`, `nro_estudiantes_col`, `nro_docentes_col`, 
    `descripcion_col`, `horario_atencion_col`
) VALUES (
    1, 'Colegio Marista "Sagrados Corazones"', 'RB-12345', 'Convenio',
    'Primaria', 'Av. Principal, Zona Central', 'Roboré',
    '9742039', 'info@maristarobore.bo', 'www.maristarobore.bo', 'Juan Pérez',
    '1985-03-10', 800, 40,
    'Colegio de convenio de alta calidad educativa dirigido por la comunidad Marista',
    'Lunes a Viernes, 7:00 - 12:00'
);

-- -----------------------------------------------------
-- Insertar configuración
-- -----------------------------------------------------
INSERT INTO `configuracion` (`id_config`, `tipo`, `telefono`, `email`, `direccion`) VALUES
(1, 'emergencia', '77777777', 'emergencia@maristarobore.bo', 'Av. Principal, Roboré');

-- -----------------------------------------------------
-- Insertar gestiones
-- -----------------------------------------------------
INSERT INTO `gestion` (`gestion`, `inicio`, `fin`, `gestion_l`, `monto_pension`, `descripcion`, `status`) VALUES
(2024, '2024-02-01', '2024-11-30', '2024', 25.00, 'Gestión 2024', 2),
(2025, '2025-02-03', '2025-12-05', '2025', 25.00, 'Gestión 2025', 1);

-- -----------------------------------------------------
-- Insertar persona ADMIN (password: 123)
-- -----------------------------------------------------
INSERT INTO `persona` (
    `id_persona`, `ci`, `nombre`, `apellido`, `sexo`, 
    `direccion_dom`, `cel`, `email`, `usuario`, 
    `password`, `status`, `id_rol`
) VALUES (
    1, '7268984', 'Rene Alejandro', 'Vasquez Vare', 'M',
    'Av. Paragua 4º Anillo', '67230415', 'reneravv1@gmail.com', 'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    1, 1
);

-- -----------------------------------------------------
-- Insertar paralelos
-- -----------------------------------------------------
INSERT INTO `paralelo` (`id_paralelo`, `nivel`, `grado`, `sigla`, `cupo`, `tutor`, `turno`) VALUES
(1, 'Primaria', 1, 'A', 30, 'Sin asignación', 'Mañana'),
(2, 'Primaria', 2, 'A', 30, 'Sin asignación', 'Mañana'),
(3, 'Primaria', 3, 'A', 30, 'Sin asignación', 'Mañana'),
(4, 'Primaria', 4, 'A', 30, 'Sin asignación', 'Mañana'),
(5, 'Primaria', 5, 'A', 30, 'Sin asignación', 'Mañana'),
(6, 'Inicial', 0, 'A', 30, 'Vanesa Yujra Samora', 'Mañana'),
(7, 'Primaria', 6, 'A', 30, 'Sin asignación', 'Mañana');

-- -----------------------------------------------------
-- Insertar materias
-- -----------------------------------------------------
INSERT INTO `materia` (`id_materia`, `area_mat`, `nombre_mat`, `descripcion_mat`, `grado`, `nivel`, `horas_mat`, `status`) VALUES
(1, 'Comunicación y Lenguaje', 'Lenguaje', 'Desarrollo de habilidades en el uso del idioma español', 1, 'Primaria', 5, 1),
(2, 'Matemática', 'Matemáticas', 'Fundamentos de aritmética, geometría y álgebra básica', 1, 'Primaria', 5, 1),
(3, 'Ciencias Sociales', 'Ciencias Sociales', 'Historia, geografía y educación cívica', 1, 'Primaria', 4, 1),
(4, 'Educación Física', 'Educación Física', 'Desarrollo físico y actividades deportivas', 1, 'Primaria', 2, 1),
(5, 'Educación Artística', 'Educación Artística', 'Expresión creativa', 1, 'Primaria', 2, 1);

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER $$

-- Procedimiento: Obtener gestión activa
DROP PROCEDURE IF EXISTS `get_gestion_activa`$$
CREATE PROCEDURE `get_gestion_activa`()
BEGIN
    SELECT * FROM gestion WHERE status = 1 LIMIT 1;
END$$

-- Procedimiento: Registrar intento de login
DROP PROCEDURE IF EXISTS `sp_registrar_intento_login`$$
CREATE PROCEDURE `sp_registrar_intento_login`(
    IN p_usuario VARCHAR(100),
    IN p_exitoso TINYINT(1),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(255)
)
BEGIN
    INSERT INTO login_intentos (usuario, exitoso, ip_address, user_agent)
    VALUES (p_usuario, p_exitoso, p_ip_address, p_user_agent);
END$$

-- Procedimiento: Verificar bloqueo de login
DROP PROCEDURE IF EXISTS `sp_verificar_bloqueo_login`$$
CREATE PROCEDURE `sp_verificar_bloqueo_login`(
    IN p_usuario VARCHAR(100),
    IN p_minutos INT,
    IN p_max_intentos INT,
    OUT p_bloqueado TINYINT(1)
)
BEGIN
    DECLARE v_intentos INT;
    
    SELECT COUNT(*) INTO v_intentos
    FROM login_intentos
    WHERE usuario = p_usuario
    AND exitoso = 0
    AND fecha_intento > DATE_SUB(NOW(), INTERVAL p_minutos MINUTE);
    
    SET p_bloqueado = IF(v_intentos >= p_max_intentos, 1, 0);
END$$

-- Procedimiento: Insertar nueva gestión
DROP PROCEDURE IF EXISTS `insertGestion`$$
CREATE PROCEDURE `insertGestion`(
    IN p_gestion BIGINT,
    IN p_inicio DATE,
    IN p_fin DATE,
    IN p_gestion_l VARCHAR(50),
    IN p_monto_pension DECIMAL(10,2),
    IN p_descripcion TEXT,
    IN p_status INT
)
BEGIN
    IF EXISTS (SELECT 1 FROM gestion WHERE status = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Ya existe una gestión activa.';
    END IF;

    IF EXISTS (SELECT 1 FROM gestion WHERE gestion = p_gestion) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El valor de "gestion" ya existe.';
    END IF;

    INSERT INTO gestion (gestion, inicio, fin, gestion_l, monto_pension, descripcion, status)
    VALUES (p_gestion, p_inicio, p_fin, p_gestion_l, p_monto_pension, p_descripcion, p_status);
END$$

-- Procedimiento: Matricular estudiante y generar pensiones
DROP PROCEDURE IF EXISTS `matricular_estudiante_y_generar_pensiones`$$
CREATE PROCEDURE `matricular_estudiante_y_generar_pensiones`(
    IN p_ci VARCHAR(30),
    IN p_gestion INT,
    IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20),
    IN p_folio TEXT,
    IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_monto_pension DECIMAL(10, 2);
    DECLARE v_id_estudiante BIGINT;
    DECLARE v_id_matricula BIGINT;
    DECLARE v_mes VARCHAR(10);
    DECLARE v_mes_index INT;

    START TRANSACTION;

    -- Buscar estudiante por CI
    SELECT e.id_estudiante INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;

    IF v_id_estudiante IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no existe o no tiene un estado activo.';
    END IF;

    -- Verificar matrícula duplicada
    IF EXISTS (
        SELECT 1 FROM matricula
        WHERE id_estudiante = v_id_estudiante
        AND gestion = p_gestion
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante ya está matriculado en esta gestión.';
    END IF;

    -- Insertar matrícula
    INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
    VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);

    SET v_id_matricula = LAST_INSERT_ID();

    -- Obtener monto de pensión
    SELECT monto_pension INTO v_monto_pension
    FROM gestion
    WHERE gestion = p_gestion AND status = 1;

    -- Generar pensiones (Febrero a Noviembre)
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

        INSERT INTO pensiones (id_matricula, monto, estado_pago, status, mes)
        VALUES (v_id_matricula, v_monto_pension, 0, 1, v_mes);

        SET v_mes_index = v_mes_index + 1;
    END WHILE;

    COMMIT;
END$$

-- =====================================================
-- MIGRACIÓN 2026-04: legajo físico del estudiante
-- - estudiante.folio_fisico INT UNSIGNED UNIQUE (identificador numérico único
--   para rastrear/autenticar/ordenar el legajo; se autogenera MAX+1)
-- - estudiante.estante / gaveta (ubicación real en el archivo físico)
-- - estudiante.estado_legajo (archivado/prestado/digitalizado/observado)
-- - matricula.folio NO cambia: es el folio del formulario anual, distinto
--   del folio del legajo (uno por estudiante).
-- =====================================================

ALTER TABLE `estudiante`
    ADD COLUMN IF NOT EXISTS `folio_fisico` INT UNSIGNED NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `estante` VARCHAR(10) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `gaveta` VARCHAR(20) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `estado_legajo` VARCHAR(20) NULL DEFAULT NULL;

-- (MySQL no soporta IF NOT EXISTS en índices; existe de antes o se crea)
-- ALTER TABLE `estudiante` ADD UNIQUE KEY `uq_folio_fisico` (`folio_fisico`);

DELIMITER ;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS COMPLETOS
-- SISTEMA DE GESTIÓN EDUCATIVA MARISTA SS.CC.
-- =====================================================

DELIMITER $$

-- =====================================================
-- PROCEDIMIENTOS DE GESTIÓN (Gestion)
-- =====================================================

-- Obtener gestión activa
DROP PROCEDURE IF EXISTS `get_gestion_activa`$$
CREATE PROCEDURE `get_gestion_activa`()
BEGIN
    SELECT * FROM gestion WHERE status = 1 LIMIT 1;
END$$

-- Obtener todas las gestiones
DROP PROCEDURE IF EXISTS `obtenerGestiones`$$
CREATE PROCEDURE `obtenerGestiones`()
BEGIN
    SELECT * FROM gestion ORDER BY gestion DESC;
END$$

-- Obtener una gestión específica
DROP PROCEDURE IF EXISTS `obtenerGestion`$$
CREATE PROCEDURE `obtenerGestion`(IN p_gestion BIGINT)
BEGIN
    SELECT gestion, inicio, fin, gestion_l, monto_pension, descripcion, status
    FROM gestion WHERE gestion = p_gestion;
END$$

-- Insertar nueva gestión
DROP PROCEDURE IF EXISTS `insertGestion`$$
CREATE PROCEDURE `insertGestion`(
    IN p_gestion BIGINT,
    IN p_inicio DATE,
    IN p_fin DATE,
    IN p_gestion_l VARCHAR(50),
    IN p_monto_pension DECIMAL(10,2),
    IN p_descripcion TEXT,
    IN p_status INT
)
BEGIN
    IF EXISTS (SELECT 1 FROM gestion WHERE status = 1) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Ya existe una gestión activa.';
    END IF;

    IF EXISTS (SELECT 1 FROM gestion WHERE gestion = p_gestion) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El valor de "gestion" ya existe.';
    END IF;

    INSERT INTO gestion (gestion, inicio, fin, gestion_l, monto_pension, descripcion, status)
    VALUES (p_gestion, p_inicio, p_fin, p_gestion_l, p_monto_pension, p_descripcion, p_status);
END$$

-- Actualizar gestión
DROP PROCEDURE IF EXISTS `updateGestion`$$
CREATE PROCEDURE `updateGestion`(
    IN p_gestion BIGINT,
    IN p_inicio DATE,
    IN p_fin DATE,
    IN p_gestion_l VARCHAR(50),
    IN p_monto_pension DECIMAL(10,2),
    IN p_descripcion TEXT
)
BEGIN
    DECLARE gestion_existe INT;

    SELECT COUNT(*) INTO gestion_existe
    FROM gestion WHERE gestion = p_gestion;

    IF gestion_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La gestión que intenta actualizar no existe.';
    ELSE
        UPDATE gestion
        SET inicio = p_inicio,
            fin = p_fin,
            gestion_l = p_gestion_l,
            monto_pension = p_monto_pension,
            descripcion = p_descripcion
        WHERE gestion = p_gestion;
    END IF;
END$$

-- Cerrar gestión activa
DROP PROCEDURE IF EXISTS `sp_close_active_gestion`$$
CREATE PROCEDURE `sp_close_active_gestion`()
BEGIN
    IF EXISTS (SELECT 1 FROM gestion WHERE status = 1) THEN
        UPDATE gestion SET status = 2 WHERE status = 1;
    ELSE
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'No existe ninguna gestión activa para cerrar.';
    END IF;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE SEGURIDAD (Login)
-- =====================================================

-- Registrar intento de login
DROP PROCEDURE IF EXISTS `sp_registrar_intento_login`$$
CREATE PROCEDURE `sp_registrar_intento_login`(
    IN p_usuario VARCHAR(100),
    IN p_exitoso TINYINT(1),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(255)
)
BEGIN
    INSERT INTO login_intentos (usuario, exitoso, ip_address, user_agent)
    VALUES (p_usuario, p_exitoso, p_ip_address, p_user_agent);
END$$

-- Verificar bloqueo de login
DROP PROCEDURE IF EXISTS `sp_verificar_bloqueo_login`$$
CREATE PROCEDURE `sp_verificar_bloqueo_login`(
    IN p_usuario VARCHAR(100),
    IN p_minutos INT,
    IN p_max_intentos INT,
    OUT p_bloqueado TINYINT(1)
)
BEGIN
    DECLARE v_intentos INT;
    
    SELECT COUNT(*) INTO v_intentos
    FROM login_intentos
    WHERE usuario = p_usuario
    AND exitoso = 0
    AND fecha_intento > DATE_SUB(NOW(), INTERVAL p_minutos MINUTE);
    
    SET p_bloqueado = IF(v_intentos >= p_max_intentos, 1, 0);
END$$

-- Limpiar logs antiguos
DROP PROCEDURE IF EXISTS `sp_limpiar_logs_antiguos`$$
CREATE PROCEDURE `sp_limpiar_logs_antiguos`(IN p_dias INT)
BEGIN
    DELETE FROM login_intentos
    WHERE fecha_intento < DATE_SUB(NOW(), INTERVAL p_dias DAY);
    
    SELECT ROW_COUNT() AS registros_eliminados;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE PERSONAS
-- =====================================================

-- Insertar persona
DROP PROCEDURE IF EXISTS `sp_insertar_persona`$$
CREATE PROCEDURE `sp_insertar_persona`(
    IN p_identificacion VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(200),
    IN p_sexo VARCHAR(10),
    IN p_direccion_dom TEXT,
    IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_usuario VARCHAR(100),
    IN p_password TEXT,
    IN p_status INT,
    IN p_id_rol BIGINT
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE email = p_email OR ci = p_identificacion;

    IF NOT persona_existe THEN
        INSERT INTO persona(ci, nombre, apellido, sexo, direccion_dom, cel, email, usuario, password, status, id_rol) 
        VALUES(p_identificacion, p_nombre, p_apellido, p_sexo, p_direccion_dom, p_cel, p_email, p_usuario, p_password, p_status, p_id_rol);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La persona ya existe';
    END IF;
END$$

-- Actualizar persona
DROP PROCEDURE IF EXISTS `sp_guardar_o_actualizar_persona`$$
CREATE PROCEDURE `sp_guardar_o_actualizar_persona`(
    IN p_id_persona BIGINT,
    IN p_identificacion VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(200),
    IN p_sexo VARCHAR(10),
    IN p_direccion_dom TEXT,
    IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_usuario VARCHAR(100),
    IN p_password TEXT,
    IN p_status INT,
    IN p_id_rol BIGINT
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE (email = p_email OR ci = p_identificacion) 
          AND id_persona != p_id_persona;

    IF persona_existe THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI o Email ya existen';
    ELSE
        IF p_password IS NULL OR p_password = '' THEN
            UPDATE persona
            SET ci = p_identificacion,
                nombre = p_nombre,
                apellido = p_apellido,
                sexo = p_sexo,
                direccion_dom = p_direccion_dom,
                cel = p_cel,
                email = p_email,
                usuario = p_usuario,
                status = p_status,
                id_rol = p_id_rol
            WHERE id_persona = p_id_persona;
        ELSE
            UPDATE persona
            SET ci = p_identificacion,
                nombre = p_nombre,
                apellido = p_apellido,
                sexo = p_sexo,
                direccion_dom = p_direccion_dom,
                cel = p_cel,
                email = p_email,
                usuario = p_usuario,
                password = p_password,
                status = p_status,
                id_rol = p_id_rol
            WHERE id_persona = p_id_persona;
        END IF;
    END IF;
END$$

-- Seleccionar personas con rol
-- Convención: 1=Activo, 2=Inactivo (ambos gestionables), 0=Eliminado (oculto)
DROP PROCEDURE IF EXISTS `sp_select_personas_con_rol`$$
CREATE PROCEDURE `sp_select_personas_con_rol`(IN esAdmin BOOLEAN)
BEGIN
    SELECT p.id_persona, p.ci, p.nombre, p.apellido, p.cel, p.email, p.status,
           r.idrol, r.nombrerol
    FROM persona p
    INNER JOIN rol r ON p.id_rol = r.idrol
    WHERE p.status IN (1,2)
    AND (esAdmin = FALSE OR p.id_persona != 1);
END$$

-- Seleccionar persona específica
DROP PROCEDURE IF EXISTS `sp_select_persona_rol`$$
CREATE PROCEDURE `sp_select_persona_rol`(IN p_persona BIGINT)
BEGIN
    SELECT p.id_persona, p.ci, p.nombre, p.apellido, p.cel, p.email,
           p.direccion_dom, p.sexo, p.usuario,
           DATE_FORMAT(p.fecha_reg, '%d-%m-%Y') AS fecha_reg,
           r.idrol, r.nombrerol, p.status
    FROM persona p
    INNER JOIN rol r ON p.id_rol = r.idrol
    WHERE p.id_persona = p_persona;
END$$

-- Validar si persona existe
DROP PROCEDURE IF EXISTS `sp_validar_persona_exist`$$
CREATE PROCEDURE `sp_validar_persona_exist`(
    IN p_email VARCHAR(100),
    IN p_ci VARCHAR(100)
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE email = p_email OR ci = p_ci;

    SELECT persona_existe AS existe;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE ESTUDIANTES
-- =====================================================

-- Registrar estudiante completo
DROP PROCEDURE IF EXISTS `sp_registrar_estudiante`$$
CREATE PROCEDURE `sp_registrar_estudiante`(
    IN p_ci VARCHAR(30),
    IN p_nombre VARCHAR(80),
    IN p_apellido VARCHAR(100),
    IN p_sexo VARCHAR(10),
    IN p_direccion_dom VARCHAR(100),
    IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_usuario VARCHAR(50),
    IN p_password TEXT,
    IN p_id_rol BIGINT,
    IN p_colegio_proc VARCHAR(100),
    IN p_rude VARCHAR(30),
    IN p_provincia VARCHAR(50),
    IN p_ciudad VARCHAR(50),
    IN p_pais VARCHAR(50),
    IN p_fnacimiento DATE,
    IN p_emergencia VARCHAR(100),
    IN p_estado_reg VARCHAR(20),
    IN p_statusGeneral INT
)
BEGIN
    DECLARE v_id_persona BIGINT;

    START TRANSACTION;

    -- Validaciones de unicidad
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado.';
    END IF;

    IF EXISTS (SELECT 1 FROM persona WHERE cel = p_cel) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El celular ya está registrado.';
    END IF;

    IF EXISTS (SELECT 1 FROM persona WHERE email = p_email) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El email ya está registrado.';
    END IF;

    -- Insertar persona
    INSERT INTO persona (ci, nombre, apellido, sexo, direccion_dom, cel, email, 
                         usuario, password, id_rol, status)
    VALUES (p_ci, p_nombre, p_apellido, p_sexo, p_direccion_dom, p_cel, p_email,
            p_usuario, p_password, p_id_rol, p_statusGeneral);

    SET v_id_persona = LAST_INSERT_ID();

    -- Validar RUDE
    IF EXISTS (SELECT 1 FROM estudiante WHERE rude = p_rude) THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado.';
    END IF;

    -- Insertar estudiante
    INSERT INTO estudiante (id_persona, colegio_proc, rude, provincia, ciudad, pais,
                            fnacimiento, emergencia, estado_reg, status)
    VALUES (v_id_persona, p_colegio_proc, p_rude, p_provincia, p_ciudad, p_pais,
            p_fnacimiento, p_emergencia, p_estado_reg, p_statusGeneral);

    COMMIT;
    SELECT 'Estudiante registrado exitosamente.' AS mensaje;
END$$

-- Listar estudiantes registrados
DROP PROCEDURE IF EXISTS `ListarEstudiantesRegistrados`$$
CREATE PROCEDURE `ListarEstudiantesRegistrados`()
BEGIN
    SELECT e.id_estudiante, e.id_persona, e.rude, p.nombre, p.apellido,
           e.estado_reg, p.email, p.cel, e.status AS status_estudiante
    FROM estudiante e
    JOIN persona p ON e.id_persona = p.id_persona
    WHERE e.status != 0;
END$$

-- Obtener información detallada de estudiante
DROP PROCEDURE IF EXISTS `ListarInformacionEstudiante`$$
CREATE PROCEDURE `ListarInformacionEstudiante`(IN estudiante_id BIGINT)
BEGIN
    SELECT p.ci, p.nombre, p.apellido, p.sexo, p.direccion_dom, p.cel, p.email,
           p.usuario, p.status AS persona_status, p.token, p.fecha_reg AS persona_fecha_reg,
           e.id_estudiante, e.colegio_proc, e.rude, e.provincia, e.ciudad, e.pais,
           e.fnacimiento, e.emergencia, e.estado_reg, e.fecha_reg AS estudiante_fecha_reg,
           e.status AS estudiante_status
    FROM persona p
    JOIN estudiante e ON p.id_persona = e.id_persona
    WHERE e.id_estudiante = estudiante_id;
END$$

-- Actualizar estudiante
DROP PROCEDURE IF EXISTS `updateEstudiante`$$
CREATE PROCEDURE `updateEstudiante`(
    IN p_idEstudiante BIGINT,
    IN p_ci VARCHAR(30),
    IN p_rude VARCHAR(30),
    IN p_estadoReg VARCHAR(20),
    IN p_nombre VARCHAR(80),
    IN p_apellido VARCHAR(100),
    IN p_sexo VARCHAR(10),
    IN p_telefono VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_direccion VARCHAR(100),
    IN p_fnacimiento DATE,
    IN p_pais VARCHAR(50),
    IN p_ciudad VARCHAR(50),
    IN p_provincia VARCHAR(50),
    IN p_colegioProc VARCHAR(100),
    IN p_emergencia VARCHAR(100),
    IN p_tipoId BIGINT,
    IN p_password TEXT,
    IN p_usuario TEXT,
    IN p_status INT
)
BEGIN
    DECLARE v_idPersona BIGINT;
    DECLARE v_rudeExists BIGINT;

    START TRANSACTION;

    SELECT id_persona INTO v_idPersona
    FROM estudiante WHERE id_estudiante = p_idEstudiante;

    IF v_idPersona IS NOT NULL THEN
        SELECT COUNT(*) INTO v_rudeExists
        FROM estudiante
        WHERE rude = p_rude AND id_estudiante != p_idEstudiante;

        IF v_rudeExists > 0 THEN
            ROLLBACK;
            SELECT 'Error: RUDE ya registrado.' AS mensaje;
        ELSE
            UPDATE persona
            SET ci = p_ci, nombre = p_nombre, apellido = p_apellido,
                sexo = p_sexo, cel = p_telefono, email = p_email,
                direccion_dom = p_direccion, id_rol = p_tipoId,
                password = p_password, usuario = p_usuario
            WHERE id_persona = v_idPersona;

            UPDATE estudiante
            SET rude = p_rude, estado_reg = p_estadoReg,
                colegio_proc = p_colegioProc, provincia = p_provincia,
                ciudad = p_ciudad, pais = p_pais, fnacimiento = p_fnacimiento,
                status = p_status, emergencia = p_emergencia
            WHERE id_estudiante = p_idEstudiante;

            COMMIT;
            SELECT 'Estudiante actualizado correctamente.' AS mensaje;
        END IF;
    ELSE
        ROLLBACK;
        SELECT 'Error: Estudiante no encontrado.' AS mensaje;
    END IF;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE MATRÍCULA
-- =====================================================

-- Matricular estudiante
DROP PROCEDURE IF EXISTS `matricular_estudiante`$$
CREATE PROCEDURE `matricular_estudiante`(
    IN p_ci VARCHAR(30),
    IN p_gestion INT,
    IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20),
    IN p_folio TEXT,
    IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_id_estudiante BIGINT;
    
    SELECT e.id_estudiante INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;
    
    IF v_id_estudiante IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no existe.';
    ELSE
        IF EXISTS (
            SELECT 1 FROM matricula
            WHERE id_estudiante = v_id_estudiante
            AND gestion = p_gestion
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El estudiante ya está matriculado en esta gestión.';
        ELSE
            INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
            VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);
        END IF;
    END IF;
END$$

-- Matricular estudiante y generar pensiones
DROP PROCEDURE IF EXISTS `matricular_estudiante_y_generar_pensiones`$$
CREATE PROCEDURE `matricular_estudiante_y_generar_pensiones`(
    IN p_ci VARCHAR(30),
    IN p_gestion INT,
    IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20),
    IN p_folio TEXT,
    IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_monto_pension DECIMAL(10, 2);
    DECLARE v_id_estudiante BIGINT;
    DECLARE v_id_matricula BIGINT;
    DECLARE v_mes VARCHAR(10);
    DECLARE v_mes_index INT;

    START TRANSACTION;

    SELECT e.id_estudiante INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;

    IF v_id_estudiante IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no existe.';
    END IF;

    IF EXISTS (
        SELECT 1 FROM matricula
        WHERE id_estudiante = v_id_estudiante
        AND gestion = p_gestion
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante ya está matriculado.';
    END IF;

    INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
    VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);

    SET v_id_matricula = LAST_INSERT_ID();

    SELECT monto_pension INTO v_monto_pension
    FROM gestion WHERE gestion = p_gestion AND status = 1
    LIMIT 1;

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

        INSERT INTO pensiones (id_matricula, monto, estado_pago, status, mes)
        VALUES (v_id_matricula, v_monto_pension, 0, 1, v_mes);

        SET v_mes_index = v_mes_index + 1;
    END WHILE;

    COMMIT;
END$$

-- Listar matrículas
DROP PROCEDURE IF EXISTS `listar_matriculas`$$
CREATE PROCEDURE `listar_matriculas`()
BEGIN
    SELECT m.id_matricula, m.gestion, m.tipo AS tipo_matricula, m.folio,
           m.estado_inscripcion, m.status AS estado_matricula,
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

-- Listar estudiantes por curso
DROP PROCEDURE IF EXISTS `listar_estudiantes_curso`$$
CREATE PROCEDURE `listar_estudiantes_curso`(
    IN idParalelo BIGINT,
    IN gestion BIGINT
)
BEGIN
    SELECT estudiante.id_estudiante, persona.ci, persona.nombre, persona.apellido,
           persona.email, persona.cel, persona.status,
           matricula.gestion, paralelo.nivel, paralelo.grado,
           paralelo.sigla, paralelo.turno, matricula.id_matricula
    FROM matricula
    JOIN paralelo ON paralelo.id_paralelo = matricula.id_paralelo
    JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
    JOIN persona ON persona.id_persona = estudiante.id_persona
    WHERE matricula.id_paralelo = idParalelo
    AND matricula.gestion = gestion
    AND matricula.status = 1
    ORDER BY persona.apellido ASC;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE PENSIONES
-- =====================================================

-- Pagar pensión
DROP PROCEDURE IF EXISTS `pagarPension`$$
CREATE PROCEDURE `pagarPension`(
    IN p_id_pension INT,
    IN p_tipo_pago VARCHAR(50),
    IN p_codigo VARCHAR(20),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_ci VARCHAR(20),
    IN p_relacion VARCHAR(50),
    IN p_monto DECIMAL(10, 2),
    IN p_fecha DATETIME
)
BEGIN
    DECLARE v_id_matricula INT;

    SELECT id_matricula INTO v_id_matricula
    FROM pensiones WHERE id_pensiones = p_id_pension;

    IF v_id_matricula IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La pensión no existe';
    END IF;

    UPDATE pensiones
    SET tipo_pago = p_tipo_pago,
        codigo = p_codigo,
        nombre = p_nombre,
        apellido = p_apellido,
        ci = p_ci,
        relacion = p_relacion,
        monto = p_monto,
        estado_pago = 1,
        fecha_reg_pago = p_fecha
    WHERE id_pensiones = p_id_pension;
END$$

-- Obtener pensiones de un estudiante
DROP PROCEDURE IF EXISTS `obtener_pensiones_estudiante`$$
CREATE PROCEDURE `obtener_pensiones_estudiante`(IN p_ci VARCHAR(20))
BEGIN
    SELECT p.id_pensiones, e.id_persona, m.id_matricula,
           per.ci AS ci_estudiante,
           CONCAT(per.nombre, ' ', per.apellido) AS nombre_completo,
           CONCAT(pa.nivel, ': ', pa.grado, ' - ', pa.sigla) AS curso,
           p.monto, p.estado_pago, m.gestion, p.mes
    FROM pensiones p
    INNER JOIN matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    INNER JOIN persona per ON e.id_persona = per.id_persona
    INNER JOIN paralelo pa ON m.id_paralelo = pa.id_paralelo
    WHERE per.ci = p_ci
    ORDER BY m.gestion DESC, pa.grado ASC;
END$$

-- Obtener detalle de pensión
DROP PROCEDURE IF EXISTS `obtener_detalle_pension`$$
CREATE PROCEDURE `obtener_detalle_pension`(IN p_id_pension BIGINT)
BEGIN
    SELECT p.id_pensiones, p.mes, p.monto, p.estado_pago,
           p.tipo_pago, p.codigo, p.nombre AS pagador_nombre,
           p.apellido AS pagador_apellido, p.ci AS pagador_ci,
           p.relacion, p.fecha_reg_pago,
           m.id_matricula, m.gestion,
           pr.ci AS estudiante_ci, pr.nombre AS estudiante_nombre,
           pr.apellido AS estudiante_apellido, pr.email AS estudiante_email
    FROM pensiones p
    INNER JOIN matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    INNER JOIN persona pr ON e.id_persona = pr.id_persona
    WHERE p.id_pensiones = p_id_pension;
END$$

-- Obtener detalle completo de pensión
DROP PROCEDURE IF EXISTS `obtener_detalle_completo_pension`$$
CREATE PROCEDURE `obtener_detalle_completo_pension`(IN p_id_pension BIGINT)
BEGIN
    SELECT p.id_pensiones, p.mes, p.monto, p.estado_pago,
           p.tipo_pago, p.codigo, p.nombre AS pagador_nombre,
           p.apellido AS pagador_apellido, p.ci AS pagador_ci,
           p.relacion, p.fecha_reg_pago,
           m.id_matricula, m.gestion, g.monto_pension,
           m.tipo AS tipo_matricula,
           pa.nivel, pa.grado, pa.sigla, pa.turno,
           e.emergencia, pr.ci AS ci_estudiante,
           pr.nombre AS nombre_estudiante, pr.apellido AS apellido_estudiante
    FROM pensiones p
    INNER JOIN matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN gestion g ON m.gestion = g.gestion
    INNER JOIN paralelo pa ON m.id_paralelo = pa.id_paralelo
    INNER JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    INNER JOIN persona pr ON e.id_persona = pr.id_persona
    WHERE p.id_pensiones = p_id_pension;
END$$

-- Obtener todas las pensiones pagadas
DROP PROCEDURE IF EXISTS `obtener_informacion_pensiones_pagadas`$$
CREATE PROCEDURE `obtener_informacion_pensiones_pagadas`()
BEGIN
    SELECT pen.id_pensiones, per.ci, m.id_matricula,
           per.nombre, per.apellido,
           CONCAT(par.nivel, ': ', par.grado, ' - ', par.sigla) AS curso,
           pen.monto, pen.fecha_reg_pago, pen.mes,
           m.gestion, pen.status,
           'Pagado' AS Estado_Pago
    FROM pensiones pen
    JOIN matricula m ON pen.id_matricula = m.id_matricula
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN persona per ON e.id_persona = per.id_persona
    JOIN paralelo par ON m.id_paralelo = par.id_paralelo
    WHERE pen.status = 1 AND pen.estado_pago = 1
    ORDER BY pen.fecha_reg_pago ASC;
END$$

-- Obtener todas las pensiones
DROP PROCEDURE IF EXISTS `obtener_informacion_pensiones_Todas`$$
CREATE PROCEDURE `obtener_informacion_pensiones_Todas`()
BEGIN
    SELECT per.ci, m.id_matricula, per.nombre, per.apellido,
           CONCAT(par.nivel, ': ', par.grado, ' - ', par.sigla) AS curso,
           pen.monto, m.gestion, pen.status,
           CASE WHEN pen.estado_pago = 1 THEN 'Pagado' ELSE 'Pendiente' END AS Estado_Pago
    FROM pensiones pen
    JOIN matricula m ON pen.id_matricula = m.id_matricula
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN persona per ON e.id_persona = per.id_persona
    JOIN paralelo par ON m.id_paralelo = par.id_paralelo
    WHERE pen.status = 1;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE MATERIAS
-- =====================================================

-- Insertar materia
DROP PROCEDURE IF EXISTS `sp_insertar_materia`$$
CREATE PROCEDURE `sp_insertar_materia`(
    IN p_area_mat VARCHAR(50),
    IN p_nombre_mat VARCHAR(50),
    IN p_descripcion_mat TEXT,
    IN p_grado INT,
    IN p_nivel VARCHAR(10),
    IN p_horas_mat INT,
    IN p_status INT
)
BEGIN
    IF EXISTS (
        SELECT 1 FROM materia 
        WHERE nombre_mat = p_nombre_mat AND grado = p_grado
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'La materia ya existe en este grado';
    END IF;
    
    INSERT INTO materia(area_mat, nombre_mat, descripcion_mat, grado, nivel, horas_mat, status)
    VALUES(p_area_mat, p_nombre_mat, p_descripcion_mat, p_grado, p_nivel, p_horas_mat, p_status);
    
    SELECT LAST_INSERT_ID() AS id_materia;
END$$

-- Actualizar materia
DROP PROCEDURE IF EXISTS `sp_actualizar_materia`$$
CREATE PROCEDURE `sp_actualizar_materia`(
    IN p_id_materia BIGINT,
    IN p_area_mat VARCHAR(50),
    IN p_nombre_mat VARCHAR(50),
    IN p_descripcion_mat TEXT,
    IN p_grado INT,
    IN p_nivel VARCHAR(10),
    IN p_horas_mat INT,
    IN p_status INT
)
BEGIN
    DECLARE materia_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO materia_existe
    FROM materia
    WHERE nombre_mat = p_nombre_mat 
        AND grado = p_grado 
        AND id_materia != p_id_materia;

    IF NOT materia_existe THEN
        IF EXISTS (SELECT 1 FROM materia WHERE id_materia = p_id_materia) THEN
            UPDATE materia
            SET area_mat = p_area_mat,
                nombre_mat = p_nombre_mat,
                descripcion_mat = p_descripcion_mat,
                grado = p_grado,
                nivel = p_nivel,
                horas_mat = p_horas_mat,
                status = p_status
            WHERE id_materia = p_id_materia;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La materia no existe';
        END IF;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe una materia con el mismo nombre y grado';
    END IF;
END$$

-- Insertar o actualizar materia (versión unificada)
DROP PROCEDURE IF EXISTS `sp_insertar_o_actualizar_materia`$$
CREATE PROCEDURE `sp_insertar_o_actualizar_materia`(
    IN p_id_materia BIGINT,
    IN p_area_mat VARCHAR(50),
    IN p_nombre_mat VARCHAR(50),
    IN p_descripcion_mat TEXT,
    IN p_grado INT,
    IN p_nivel VARCHAR(10),
    IN p_horas_mat INT,
    IN p_status INT
)
BEGIN
    DECLARE materia_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO materia_existe
    FROM materia
    WHERE nombre_mat = p_nombre_mat AND grado = p_grado 
    AND (p_id_materia IS NULL OR id_materia != p_id_materia);

    IF NOT materia_existe THEN
        IF p_id_materia IS NULL THEN
            INSERT INTO materia(area_mat, nombre_mat, descripcion_mat, grado, nivel, horas_mat, status)
            VALUES(p_area_mat, p_nombre_mat, p_descripcion_mat, p_grado, p_nivel, p_horas_mat, p_status);
        ELSE
            UPDATE materia
            SET area_mat = p_area_mat,
                nombre_mat = p_nombre_mat,
                descripcion_mat = p_descripcion_mat,
                grado = p_grado,
                nivel = p_nivel,
                horas_mat = p_horas_mat,
                status = p_status
            WHERE id_materia = p_id_materia;
        END IF;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La materia ya existe en este grado';
    END IF;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE PARALELOS
-- =====================================================

-- Contar inscritos por paralelo
DROP PROCEDURE IF EXISTS `sp_contar_inscritos_paralelo_por_gestion`$$
CREATE PROCEDURE `sp_contar_inscritos_paralelo_por_gestion`(IN p_gestion BIGINT)
BEGIN
    SELECT p.id_paralelo, p.nivel, p.grado, p.sigla, p.cupo,
           p.fecha_reg, p.tutor, p.turno, p.status,
           COUNT(m.id_estudiante) AS total_inscritos
    FROM paralelo p
    LEFT JOIN matricula m ON p.id_paralelo = m.id_paralelo
    WHERE (m.gestion = p_gestion OR m.gestion IS NULL)
    GROUP BY p.id_paralelo
    ORDER BY p.nivel, p.grado, p.sigla;
END$$

-- Obtener información de un paralelo específico
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
    LEFT JOIN matricula m ON p.id_paralelo = m.id_paralelo
    WHERE (m.gestion = p_gestion OR m.gestion IS NULL)
    AND p.id_paralelo = p_paralelo;
END$$

-- =====================================================
-- PROCEDIMIENTOS DE COLEGIO
-- =====================================================

-- Obtener información del colegio
DROP PROCEDURE IF EXISTS `obtener_colegio_por_id`$$
CREATE PROCEDURE `obtener_colegio_por_id`(IN p_id_colegio BIGINT)
BEGIN
    SELECT id_colegio, nombre_col, codigo_registro_col, tipo_col,
           nivel_educativo_col, direccion_col, ciudad_col,
           telefono_col, correo_col, sitio_web_col, director_col,
           fecha_creacion_col, nro_estudiantes_col, nro_docentes_col,
           descripcion_col, horario_atencion_col, status
    FROM colegio
    WHERE id_colegio = p_id_colegio AND status = 1;
END$$

-- =====================================================
-- PROCEDIMIENTO DE PRUEBA
-- =====================================================

DROP PROCEDURE IF EXISTS `test_signal`$$
CREATE PROCEDURE `test_signal`()
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Mensaje de error personalizado';
END$$

DELIMITER ;

-- =====================================================
-- VERIFICACIÓN FINAL
-- =====================================================

SELECT '✅ Base de datos DB_marista creada correctamente' AS 'Mensaje';
SELECT CONCAT('📊 Total procedimientos: ', COUNT(*)) AS 'Resumen'
FROM information_schema.routines
WHERE routine_schema = 'DB_marista'
AND routine_type = 'PROCEDURE';

-- =====================================================
-- MIGRACIÓN 2026-02: foto de estudiante + lista enriquecida
-- - estudiante.foto (ruta relativa en Assets/images/uploads/estudiantes/)
-- - sp_registrar_estudiante / updateEstudiante aceptan p_foto
-- - ListarEstudiantesRegistrados: foto, curso actual (gestión activa), n° tutores
-- - ListarInformacionEstudiante: foto
-- =====================================================

ALTER TABLE `estudiante` ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) NULL DEFAULT NULL AFTER `emergencia`;

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_registrar_estudiante`$$
CREATE PROCEDURE `sp_registrar_estudiante`(
    IN p_ci VARCHAR(30), IN p_nombre VARCHAR(80), IN p_apellido VARCHAR(100),
    IN p_sexo VARCHAR(10), IN p_direccion_dom VARCHAR(100), IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100), IN p_usuario VARCHAR(50), IN p_password TEXT, IN p_id_rol BIGINT,
    IN p_colegio_proc VARCHAR(100), IN p_rude VARCHAR(30), IN p_provincia VARCHAR(50),
    IN p_ciudad VARCHAR(50), IN p_pais VARCHAR(50), IN p_fnacimiento DATE,
    IN p_emergencia VARCHAR(100), IN p_estado_reg VARCHAR(20), IN p_statusGeneral INT,
    IN p_foto VARCHAR(255)
)
BEGIN
    DECLARE v_id_persona BIGINT;
    START TRANSACTION;
    IF EXISTS (SELECT 1 FROM persona WHERE ci = p_ci) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CI ya está registrado.';
    END IF;
    IF EXISTS (SELECT 1 FROM persona WHERE cel = p_cel) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El celular ya está registrado.';
    END IF;
    IF EXISTS (SELECT 1 FROM persona WHERE email = p_email) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El email ya está registrado.';
    END IF;
    INSERT INTO persona (ci, nombre, apellido, sexo, direccion_dom, cel, email, usuario, password, id_rol, status)
    VALUES (p_ci, p_nombre, p_apellido, p_sexo, p_direccion_dom, p_cel, p_email, p_usuario, p_password, p_id_rol, p_statusGeneral);
    SET v_id_persona = LAST_INSERT_ID();
    IF EXISTS (SELECT 1 FROM estudiante WHERE rude = p_rude) THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado.';
    END IF;
    INSERT INTO estudiante (id_persona, colegio_proc, rude, provincia, ciudad, pais, fnacimiento, emergencia, estado_reg, status, foto)
    VALUES (v_id_persona, p_colegio_proc, p_rude, p_provincia, p_ciudad, p_pais, p_fnacimiento, p_emergencia, p_estado_reg, p_statusGeneral, p_foto);
    COMMIT;
    SELECT 'Estudiante registrado exitosamente.' AS mensaje;
END$$

DROP PROCEDURE IF EXISTS `updateEstudiante`$$
CREATE PROCEDURE `updateEstudiante`(
    IN p_idEstudiante BIGINT, IN p_ci VARCHAR(30), IN p_rude VARCHAR(30), IN p_estadoReg VARCHAR(20),
    IN p_nombre VARCHAR(80), IN p_apellido VARCHAR(100), IN p_sexo VARCHAR(10), IN p_telefono VARCHAR(15),
    IN p_email VARCHAR(100), IN p_direccion VARCHAR(100), IN p_fnacimiento DATE, IN p_pais VARCHAR(50),
    IN p_ciudad VARCHAR(50), IN p_provincia VARCHAR(50), IN p_colegioProc VARCHAR(100),
    IN p_emergencia VARCHAR(100), IN p_tipoId BIGINT, IN p_password TEXT, IN p_usuario TEXT, IN p_status INT,
    IN p_foto VARCHAR(255)
)
BEGIN
    DECLARE v_idPersona BIGINT;
    DECLARE v_rudeExists BIGINT;
    START TRANSACTION;
    SELECT id_persona INTO v_idPersona FROM estudiante WHERE id_estudiante = p_idEstudiante;
    IF v_idPersona IS NOT NULL THEN
        SELECT COUNT(*) INTO v_rudeExists FROM estudiante WHERE rude = p_rude AND id_estudiante != p_idEstudiante;
        IF v_rudeExists > 0 THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El RUDE ya está registrado en otro estudiante.';
        ELSE
            UPDATE persona SET ci=p_ci, nombre=p_nombre, apellido=p_apellido, sexo=p_sexo, cel=p_telefono,
                email=p_email, direccion_dom=p_direccion, id_rol=p_tipoId, password=p_password, usuario=p_usuario
            WHERE id_persona = v_idPersona;
            UPDATE estudiante SET rude=p_rude, estado_reg=p_estadoReg, colegio_proc=p_colegioProc,
                provincia=p_provincia, ciudad=p_ciudad, pais=p_pais, fnacimiento=p_fnacimiento,
                status=p_status, emergencia=p_emergencia, foto=p_foto
            WHERE id_estudiante = p_idEstudiante;
            COMMIT;
            SELECT 'Estudiante actualizado correctamente.' AS mensaje;
        END IF;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Estudiante no encontrado.';
    END IF;
END$$

DROP PROCEDURE IF EXISTS `ListarEstudiantesRegistrados`$$
CREATE PROCEDURE `ListarEstudiantesRegistrados`()
BEGIN
    SELECT e.id_estudiante, e.id_persona, e.rude, e.foto, p.ci, p.nombre, p.apellido,
           e.estado_reg, p.email, p.cel, e.status AS status_estudiante,
           CASE WHEN pa.nivel = 'Inicial' THEN CONCAT('Inicial "', pa.sigla, '"')
                ELSE CONCAT(pa.nivel, ' ', pa.grado, ' "', pa.sigla, '"') END AS curso_actual,
           pa.id_paralelo AS id_paralelo_actual,
           (SELECT COUNT(*) FROM padre WHERE id_estudiante = e.id_estudiante AND status != 0) AS tutores
    FROM estudiante e
    JOIN persona p ON e.id_persona = p.id_persona
    LEFT JOIN matricula m ON m.id_estudiante = e.id_estudiante AND m.status = 1
        AND m.gestion = (SELECT gestion FROM gestion WHERE status = 1 LIMIT 1)
    LEFT JOIN paralelo pa ON pa.id_paralelo = m.id_paralelo
    WHERE e.status != 0;
END$$

DROP PROCEDURE IF EXISTS `ListarInformacionEstudiante`$$
CREATE PROCEDURE `ListarInformacionEstudiante`(IN estudiante_id BIGINT)
BEGIN
    SELECT p.ci, p.nombre, p.apellido, p.sexo, p.direccion_dom, p.cel, p.email,
           p.usuario, p.status AS persona_status, p.token, p.fecha_reg AS persona_fecha_reg,
           e.id_estudiante, e.colegio_proc, e.rude, e.provincia, e.ciudad, e.pais,
           e.fnacimiento, e.emergencia, e.estado_reg, e.fecha_reg AS estudiante_fecha_reg,
           e.status AS estudiante_status, e.foto
    FROM persona p
    JOIN estudiante e ON p.id_persona = e.id_persona
    WHERE e.id_estudiante = estudiante_id;
END$$

-- =====================================================
-- MIGRACIÓN 2026-03: doctrina de estados + matrícula estricta
-- - Solo estudiante.status=1 puede matricularse (ANTES: el SELECT INTO sin filas
--   no ponía NULL y dejaba pasar inactivos; ahora con EXISTS).
-- - Mensajes distintos: 'no existe' vs 'no está activo'.
-- - Acepta gestiones cerradas para el monto (históricos).
-- - persona.status (acceso) NO se toca desde el módulo Estudiante.
-- =====================================================
DROP PROCEDURE IF EXISTS `matricular_estudiante_y_generar_pensiones`$$
CREATE PROCEDURE `matricular_estudiante_y_generar_pensiones`(
    IN p_ci VARCHAR(30),
    IN p_gestion INT,
    IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20),
    IN p_folio TEXT,
    IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_monto_pension DECIMAL(10, 2);
    DECLARE v_id_estudiante BIGINT;
    DECLARE v_id_matricula BIGINT;
    DECLARE v_mes VARCHAR(10);
    DECLARE v_mes_index INT;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM estudiante e JOIN persona p ON p.id_persona = e.id_persona WHERE p.ci = p_ci AND e.status != 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estudiante no existe.';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM estudiante e JOIN persona p ON p.id_persona = e.id_persona WHERE p.ci = p_ci AND e.status = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El estudiante no está activo: no puede matricularse ni generar pensiones.';
    END IF;

    SELECT e.id_estudiante INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;

    IF EXISTS (
        SELECT 1 FROM matricula
        WHERE id_estudiante = v_id_estudiante
        AND gestion = p_gestion
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante ya está matriculado.';
    END IF;

    INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
    VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);

    SET v_id_matricula = LAST_INSERT_ID();

    SELECT monto_pension INTO v_monto_pension
    FROM gestion WHERE gestion = p_gestion
    LIMIT 1;

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

        INSERT INTO pensiones (id_matricula, monto, estado_pago, status, mes)
        VALUES (v_id_matricula, v_monto_pension, 0, 1, v_mes);

        SET v_mes_index = v_mes_index + 1;
    END WHILE;

    COMMIT;
END$$

-- =====================================================
-- MIGRACIÓN 2026-05: mensualidades con vencimiento, folio y auditoría
-- - pensiones.mes_num, fecha_vencimiento (fin de mes de su gestión)
-- - pensiones.nro_recibo UNIQUE (folio secuencial del comprobante)
-- - pensiones.id_usuario (cajero que cobró)
-- - pension_movimientos (auditoría pago/anulación con motivo y usuario)
-- - pagarPension recibe folio+cajero; matrícula genera mes_num+vencimiento;
--   consultas exponen vencida + orden calendario + cajero
-- =====================================================

ALTER TABLE `pensiones`
    ADD COLUMN IF NOT EXISTS `mes_num` TINYINT NULL DEFAULT NULL AFTER `mes`,
    ADD COLUMN IF NOT EXISTS `fecha_vencimiento` DATE NULL DEFAULT NULL AFTER `mes_num`,
    ADD COLUMN IF NOT EXISTS `nro_recibo` INT UNSIGNED NULL DEFAULT NULL AFTER `codigo`,
    ADD COLUMN IF NOT EXISTS `id_usuario` BIGINT NULL DEFAULT NULL AFTER `ci`;

-- (MySQL no soporta IF NOT EXISTS en índices/ tablas: crear si faltan)
-- ALTER TABLE `pensiones` ADD UNIQUE KEY `uq_nro_recibo` (`nro_recibo`);
-- CREATE TABLE IF NOT EXISTS `pension_movimientos` (...ver modelo PensionesModel...);

DELIMITER ;