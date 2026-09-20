-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: db_mr_sistm
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrativo`
--

DROP TABLE IF EXISTS `administrativo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrativo` (
  `id_admin` bigint NOT NULL AUTO_INCREMENT,
  `id_cargo` bigint NOT NULL,
  `id_persona` bigint NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_retiro` date DEFAULT NULL,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id_admin`),
  KEY `id_cargo` (`id_cargo`),
  KEY `id_persona` (`id_persona`),
  CONSTRAINT `administrativo_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
  CONSTRAINT `administrativo_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrativo`
--

LOCK TABLES `administrativo` WRITE;
/*!40000 ALTER TABLE `administrativo` DISABLE KEYS */;
/*!40000 ALTER TABLE `administrativo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_docente`
--

DROP TABLE IF EXISTS `asignacion_docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_docente` (
  `id_asignacion` bigint NOT NULL AUTO_INCREMENT,
  `id_materia` bigint DEFAULT NULL,
  `id_docente` bigint DEFAULT NULL,
  `id_paralelo` bigint DEFAULT NULL,
  `anio_lectivo` year NOT NULL,
  `horas_asignadas` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  PRIMARY KEY (`id_asignacion`),
  KEY `id_paralelo` (`id_paralelo`),
  KEY `id_materia` (`id_materia`),
  KEY `id_docente` (`id_docente`),
  CONSTRAINT `asignacion_docente_ibfk_1` FOREIGN KEY (`id_paralelo`) REFERENCES `paralelo` (`id_paralelo`),
  CONSTRAINT `asignacion_docente_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`),
  CONSTRAINT `asignacion_docente_ibfk_3` FOREIGN KEY (`id_docente`) REFERENCES `docente` (`id_docente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_docente`
--

LOCK TABLES `asignacion_docente` WRITE;
/*!40000 ALTER TABLE `asignacion_docente` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignacion_docente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_materia`
--

DROP TABLE IF EXISTS `asignacion_materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_materia` (
  `id_asignacion` bigint NOT NULL AUTO_INCREMENT,
  `id_matricula` bigint NOT NULL,
  `id_materia` bigint NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `state` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_asignacion`),
  UNIQUE KEY `id_matricula` (`id_matricula`,`id_materia`),
  KEY `fk_asignacion_materia_materia` (`id_materia`),
  CONSTRAINT `fk_asignacion_materia_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asignacion_materia_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_materia`
--

LOCK TABLES `asignacion_materia` WRITE;
/*!40000 ALTER TABLE `asignacion_materia` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignacion_materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo` (
  `id_cargo` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `salario_base` decimal(10,2) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

LOCK TABLES `cargo` WRITE;
/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
INSERT INTO `cargo` VALUES (1,'Director','Responsable de la dirección general del centro educativo',1,5000.00,'2024-01-01 00:00:00'),(2,'Subdirector','Asiste al director y supervisa algunas áreas específicas',1,4000.00,'2024-02-01 00:00:00'),(3,'Secretario','Encargado de las tareas administrativas y de organización',1,2500.00,'2024-05-01 00:00:00'),(4,'Coordinador de Actividades','Organiza y gestiona las actividades extracurriculares',1,3500.00,'2024-06-01 00:00:00');
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro`
--

DROP TABLE IF EXISTS `cobro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro` (
  `id_cobros` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `tipo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `ncuota` int NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_cobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro`
--

LOCK TABLES `cobro` WRITE;
/*!40000 ALTER TABLE `cobro` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colegio`
--

DROP TABLE IF EXISTS `colegio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colegio` (
  `id_colegio` bigint NOT NULL AUTO_INCREMENT,
  `nombre_col` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `codigo_registro_col` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `tipo_col` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nivel_educativo_col` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `direccion_col` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `ciudad_col` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `telefono_col` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `correo_col` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `sitio_web_col` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `director_col` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `fecha_creacion_col` date DEFAULT NULL,
  `nro_estudiantes_col` int DEFAULT NULL,
  `nro_docentes_col` int DEFAULT NULL,
  `descripcion_col` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `horario_atencion_col` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_colegio`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colegio`
--

LOCK TABLES `colegio` WRITE;
/*!40000 ALTER TABLE `colegio` DISABLE KEYS */;
INSERT INTO `colegio` VALUES (1,'Colegio Marista \"Sagrados Corazones\" ','RB-12345','Convenio','Primaria','Av. Principal, Zona Central','Roboré','9742039','info@maristarobore.bo','www.maristarobore.bo','Juan Pérez','1985-03-10',800,40,'Colegio de convenio de alta calidad educativa dirigido por la comunidad Marista, ubicado en Roboré, Bolivia.','Lunes a Viernes, 7:00 - 12:00',1);
/*!40000 ALTER TABLE `colegio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `id_config` bigint NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text,
  `valor` text,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES (1,'emergencia','77777777','emergencia@maristarobore.bo','Av. Principal, Roboré',NULL,1);
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docente`
--

DROP TABLE IF EXISTS `docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docente` (
  `id_docente` bigint NOT NULL AUTO_INCREMENT,
  `id_persona` bigint NOT NULL,
  `titulo_academico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `especialidad` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `años_experiencia` int NOT NULL,
  `nivel_educativo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `fecha_contratacion` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `nota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id_docente`),
  KEY `id_persona` (`id_persona`),
  CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docente`
--

LOCK TABLES `docente` WRITE;
/*!40000 ALTER TABLE `docente` DISABLE KEYS */;
/*!40000 ALTER TABLE `docente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiante`
--

DROP TABLE IF EXISTS `estudiante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudiante` (
  `id_estudiante` bigint NOT NULL AUTO_INCREMENT,
  `id_persona` bigint NOT NULL,
  `colegio_proc` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `rude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `provincia` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `ciudad` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `pais` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `fnacimiento` date NOT NULL,
  `emergencia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `estado_reg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id_estudiante`),
  UNIQUE KEY `unique_rude` (`rude`),
  KEY `id_persona` (`id_persona`),
  CONSTRAINT `estudiante_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiante`
--

LOCK TABLES `estudiante` WRITE;
/*!40000 ALTER TABLE `estudiante` DISABLE KEYS */;
INSERT INTO `estudiante` VALUES (1,2,'Unidad Educativa Loyola','RUDE072','Chapare','Cochabamba','Bolivia','2004-08-20','Carla Sánchez, 76543321','Registrado','2024-11-22 18:25:38',1),(2,3,'Unidad Educativa América','RUDE073','Warnes','Santa Cruz','Bolivia','2006-02-10','Jorge Villca, 76543432','Registrado','2024-11-22 18:25:38',1),(3,4,'Unidad Educativa Bolivia','777','Omasuyos','La Paz','Bolivia','2007-11-18','María Gómez, 76543543','Nuevo','2024-11-22 18:25:38',1),(4,5,'Unidad Educativa Alemana','8787','Quillacollo','Cochabamba','Bolivia','2005-01-30','Ana Flores, 76543654','Nuevo','2024-11-22 18:25:38',1),(5,6,'Unidad Educativa Don Bosco','4654654','Warnes','Santa Cruz','Bolivia','2003-05-25','Carlos Mendoza, 76543765','Nuevo','2024-11-22 18:25:38',1),(6,7,'Unidad Educativa Modelo','44658741','Achacachi','La Paz','Bolivia','2006-09-14','José López, 76543876','Nuevo','2024-11-22 18:25:38',1),(7,8,'Unidad Educativa Loyola','RUDE008','Colcapirhua','Cochabamba','Bolivia','2004-03-12','Mónica Rojas, 76543987','Registrado','2024-11-22 18:25:38',1),(8,9,'Unidad Educativa Anglo','RUDE009','Porongo','Santa Cruz','Bolivia','2005-12-21','Mario Ríos, 76544098','Registrado','2024-11-22 18:25:38',1),(9,10,'Unidad Educativa Victoria','RUDE010','Pacajes','La Paz','Bolivia','2003-07-28','Laura Pérez, 76544109','Registrado','2024-11-22 18:25:38',1),(10,11,'Unidad Educativa Adventista','RUDE011','Sacaba','Cochabamba','Bolivia','2006-04-08','Luis Aguilar, 76544210','Registrado','2024-11-22 18:25:38',1),(11,12,'Unidad Educativa Alemana','RUDE012','Montero','Santa Cruz','Bolivia','2005-10-17','Patricia Vargas, 76544321','Registrado','2024-11-22 18:25:38',1),(12,13,'Unidad Educativa María Auxiliadora','RUDE013','Camacho','La Paz','Bolivia','2004-06-05','Daniel Jiménez, 76544432','Registrado','2024-11-22 18:25:38',1),(13,14,'Unidad Educativa Loyola','RUDE014','Tiquipaya','Cochabamba','Bolivia','2003-08-11','Ana Quispe, 76544543','Registrado','2024-11-22 18:25:38',1),(14,15,'Unidad Educativa Don Bosco','RUDE015','La Guardia','Santa Cruz','Bolivia','2007-01-09','Pedro Vargas, 76544654','Registrado','2024-11-22 18:25:38',1),(15,16,'Unidad Educativa Santa María','RUDE016','Inquisivi','La Paz','Bolivia','2005-11-19','Carla Gutiérrez, 76544765','Registrado','2024-11-22 18:25:38',1),(16,17,'Unidad Educativa Adventista','RUDE017','Vinto','Cochabamba','Bolivia','2004-02-28','Juan Rivera, 76544876','Registrado','2024-11-22 18:25:38',1),(17,18,'Unidad Educativa Alemana','RUDE018','El Torno','Santa Cruz','Bolivia','2006-07-23','Mónica Arias, 76544987','Registrado','2024-11-22 18:25:38',1),(18,19,'Unidad Educativa Loyola','RUDE019','Los Andes','La Paz','Bolivia','2003-05-18','Carlos Salazar, 76545098','Registrado','2024-11-22 18:25:38',1),(19,20,'Unidad Educativa Victoria','RUDE020','Sipe Sipe','Cochabamba','Bolivia','2005-03-30','Sara Arce, 76545109','Registrado','2024-11-22 18:25:38',1),(20,21,'Unidad Educativa Don Bosco','RUDE021','Vallegrande','Santa Cruz','Bolivia','2004-09-07','Luis Vargas, 76545210','Registrado','2024-11-22 18:25:38',1),(21,22,'Unidad Educativa Marista','RUDE022','Palca','La Paz','Bolivia','2007-06-14','Patricia López, 76545321','Registrado','2024-11-22 18:25:38',1),(22,23,'Unidad Educativa Loyola','RUDE023','Punata','Cochabamba','Bolivia','2006-01-25','Carlos Fernández, 76545432','Registrado','2024-11-22 18:25:38',1),(23,24,'Unidad Educativa Modelo','RUDE024','San Ignacio','Santa Cruz','Bolivia','2003-11-12','Laura Rivera, 76545543','Registrado','2024-11-22 18:25:38',1),(24,25,'Unidad Educativa Alemana','RUDE025','Sorata','La Paz','Bolivia','2005-08-01','Luis Ramos, 76545654','Registrado','2024-11-22 18:25:38',1),(25,26,'Unidad Educativa Victoria','RUDE026','Villa Tunari','Cochabamba','Bolivia','2004-10-21','Pedro Quispe, 76545765','Registrado','2024-11-22 18:25:38',1),(26,27,'Unidad Educativa Adventista','RUDE027','Cotoca','Santa Cruz','Bolivia','2006-04-06','Ana Vargas, 76545876','Registrado','2024-11-22 18:25:38',1),(27,28,'Unidad Educativa María Auxiliadora','RUDE028','Cairoma','La Paz','Bolivia','2003-02-14','Luis Romero, 76545987','Registrado','2024-11-22 18:25:38',1),(28,29,'Unidad Educativa Don Bosco','RUDE029','Villa Serrano','Santa Cruz','Bolivia','2005-09-27','Laura Suárez, 76546098','Registrado','2024-11-22 18:25:38',1),(29,30,'Unidad Educativa Loyola','RUDE030','Ayopaya','Cochabamba','Bolivia','2004-12-22','Mario Álvarez, 76546109','Registrado','2024-11-22 18:25:38',1),(30,32,'marista','7268984','Arani','Arani','Bolivia','1997-11-24','No molestar','Nuevo','2024-11-22 18:34:49',1),(31,33,'Barcelona','101010','Buenos Aires','Buenos Aires','Argentina','1990-10-10','Llamar a su esposa','Nuevo','2024-11-23 19:59:47',1),(32,37,'Real Madrid','11','Argentina','Argentina','Argentina','1990-11-11','Llamar a Messi','Nuevo','2024-11-23 20:07:24',1);
/*!40000 ALTER TABLE `estudiante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `id_evento` bigint NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `lugar` varchar(200) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_evento`),
  KEY `idx_fecha` (`fecha_inicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gestion`
--

DROP TABLE IF EXISTS `gestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gestion` (
  `gestion` bigint NOT NULL,
  `inicio` date NOT NULL,
  `fin` date NOT NULL,
  `gestion_l` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `monto_pension` decimal(10,2) DEFAULT '36.00',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `status` int DEFAULT '1',
  PRIMARY KEY (`gestion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gestion`
--

LOCK TABLES `gestion` WRITE;
/*!40000 ALTER TABLE `gestion` DISABLE KEYS */;
INSERT INTO `gestion` VALUES (2022,'2022-02-01','2022-11-30','2022',36.00,'Año lectivo 2025',0),(2023,'2023-02-01','2023-11-30','2023',36.00,'Año lectivo 2023',0),(2024,'2024-02-01','2024-11-30','2024',25.00,'LP',2),(2025,'2025-02-03','2025-12-05','2025',25.00,'Gestión 2025',1);
/*!40000 ALTER TABLE `gestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_intentos`
--

DROP TABLE IF EXISTS `login_intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_intentos` (
  `id_intento` bigint NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) COLLATE utf8mb4_swedish_ci NOT NULL,
  `exitoso` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `fecha_intento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_intento`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_fecha` (`fecha_intento`),
  KEY `idx_ip` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_intentos`
--

LOCK TABLES `login_intentos` WRITE;
/*!40000 ALTER TABLE `login_intentos` DISABLE KEYS */;
INSERT INTO `login_intentos` VALUES (7,'admin',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0','2025-12-13 14:50:03');
/*!40000 ALTER TABLE `login_intentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materia`
--

DROP TABLE IF EXISTS `materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materia` (
  `id_materia` bigint NOT NULL AUTO_INCREMENT,
  `area_mat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nombre_mat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `descripcion_mat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `grado` int DEFAULT NULL,
  `nivel` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `horas_mat` int DEFAULT NULL,
  `status` int DEFAULT '1',
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_materia`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materia`
--

LOCK TABLES `materia` WRITE;
/*!40000 ALTER TABLE `materia` DISABLE KEYS */;
INSERT INTO `materia` VALUES (1,'Comunicación y Lenguaje','Lenguaje','Desarrollo de habilidades en el uso del idioma español',1,'Primaria',5,1,'2024-11-03 21:54:51'),(2,'Comunicación y Lenguaje','Lengua Extranjera (Inglés)','Introducción al idioma inglés',2,'Primaria',3,0,'2024-11-03 21:54:51'),(3,'Matemática','Matemáticas','Fundamentos de aritmética, geometría y álgebra básica',1,'Primaria',5,1,'2024-11-03 21:54:51'),(4,'Ciencias Naturales','Ciencias Naturales','Estudio de fenómenos naturales y cuidado del medio ambiente',2,'Primaria',4,0,'2024-11-03 21:54:51'),(5,'Ciencias Sociales','Ciencias Sociales','Historia, geografía y educación cívica',1,'Primaria',4,1,'2024-11-03 21:54:51'),(6,'Educación Física','Educación Física','Desarrollo físico y actividades deportivas',1,'Primaria',2,1,'2024-11-03 21:54:51'),(7,'Educación Artística','Educación Artística','Expresión creativa a través de artes plásticas, música y danza',1,'Primaria',2,1,'2024-11-03 21:54:51'),(8,'Tecnología','Educación Tecnológica','Introducción a herramientas tecnológicas básicas',1,'Secundaria',2,1,'2024-11-03 21:54:51'),(9,'Valores','Educación en Valores y Ética','Fomento de valores y principios éticos',2,'Secundaria',1,0,'2024-11-03 21:54:51'),(10,'Quimica','ASD TOPO','parangarecutirimicuaro',3,'Secundaria',1,0,'2024-11-03 23:17:28'),(11,'Quimica','Solido','',5,'Primaria',1,1,'2024-11-03 23:18:09'),(12,'Sistemas','Sistemas','Sistemas',1,'Primaria',0,1,'2024-11-03 23:19:42'),(13,'Sistemas','Sistemas','',2,'Primaria',0,0,'2024-11-03 23:20:06');
/*!40000 ALTER TABLE `materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matricula`
--

DROP TABLE IF EXISTS `matricula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matricula` (
  `id_matricula` bigint NOT NULL AUTO_INCREMENT,
  `id_estudiante` bigint DEFAULT NULL,
  `id_paralelo` bigint DEFAULT NULL,
  `id_user` bigint DEFAULT NULL,
  `gestion` bigint NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `folio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado_inscripcion` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_matricula`),
  KEY `id_estudiante` (`id_estudiante`),
  KEY `id_paralelo` (`id_paralelo`),
  KEY `gestion` (`gestion`),
  KEY `idx_matricula_estudiante_gestion` (`id_estudiante`,`gestion`),
  CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `paralelo` (`id_paralelo`),
  CONSTRAINT `matricula_ibfk_3` FOREIGN KEY (`gestion`) REFERENCES `gestion` (`gestion`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matricula`
--

LOCK TABLES `matricula` WRITE;
/*!40000 ALTER TABLE `matricula` DISABLE KEYS */;
INSERT INTO `matricula` VALUES (32,31,1,1,2025,'Regular','0','2024-11-25 09:48:30','Confirmado',1),(33,32,2,1,2025,'Regular','0','2024-11-25 12:27:42','Confirmado',1),(34,1,1,1,2025,'Regular','','2024-11-26 18:14:53','Inscrito',1),(35,2,1,1,2025,'Regular','','2024-11-26 18:14:59','Inscrito',1),(36,3,1,1,2025,'Regular','','2024-11-26 18:15:01','Inscrito',1),(37,4,1,1,2025,'Regular','','2024-11-26 18:15:05','Inscrito',1),(38,6,1,1,2025,'Regular','','2024-11-26 18:15:26','Inscrito',1),(39,5,1,1,2025,'Regular','','2024-11-26 18:15:27','Inscrito',1),(40,8,1,1,2025,'Regular','','2024-11-26 18:15:31','Inscrito',1),(41,7,1,1,2025,'Regular','','2024-11-26 18:15:31','Inscrito',1),(42,9,2,1,2025,'Regular','','2024-11-26 18:15:32','Inscrito',1),(43,10,2,1,2025,'Regular','','2024-11-26 18:15:33','Inscrito',1),(44,11,2,1,2025,'Regular','','2024-11-26 18:15:33','Inscrito',1),(45,12,2,1,2025,'Regular','','2024-11-26 18:15:34','Inscrito',1),(46,13,2,1,2025,'Regular','','2024-11-26 18:15:35','Inscrito',1),(47,14,2,1,2025,'Regular','','2024-11-26 18:15:35','Inscrito',1),(48,15,2,1,2025,'Regular','','2024-11-26 18:15:36','Inscrito',1),(49,16,2,1,2025,'Regular','','2024-11-26 18:15:36','Inscrito',1),(50,17,3,1,2025,'Regular','','2024-11-26 18:15:37','Inscrito',1),(51,18,3,1,2025,'Regular','','2024-11-26 18:15:37','Inscrito',1),(52,19,3,1,2025,'Regular','','2024-11-26 18:15:38','Inscrito',1),(53,20,3,1,2025,'Regular','','2024-11-26 18:15:38','Inscrito',1),(54,21,3,1,2025,'Regular','','2024-11-26 18:15:39','Inscrito',1),(55,22,3,1,2025,'Regular','','2024-11-26 18:15:39','Inscrito',1),(56,23,3,1,2025,'Regular','','2024-11-26 18:15:40','Inscrito',1),(57,24,3,1,2025,'Regular','','2024-11-26 18:15:40','Inscrito',1),(58,25,4,1,2025,'Regular','','2024-11-26 18:15:41','Inscrito',1),(59,26,4,1,2025,'Regular','','2024-11-26 18:15:41','Inscrito',1),(60,27,4,1,2025,'Regular','','2024-11-26 18:15:45','Inscrito',1),(61,28,4,1,2025,'Regular','','2024-11-26 18:15:46','Inscrito',1),(62,29,4,1,2025,'Regular','','2024-11-26 18:15:46','Inscrito',1),(63,30,4,1,2025,'Regular','','2024-11-26 18:15:48','Inscrito',1);
/*!40000 ALTER TABLE `matricula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo` (
  `idmodulo` bigint NOT NULL AUTO_INCREMENT,
  `titulo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idmodulo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (1,'Dashboard','Dashboard',1),(2,'Usuarios','Usuarios del sistema',1),(3,'Estudiantes','Estudiantes con acceso al sistema',1),(4,'Matricula','Registro de inscripción del estudiante',1),(5,'Pagos Mensualidad','Pagos de mensualidad realizados por los estudiantes',1),(6,'Cobros','Cobros a realizar',1),(7,'Cursos','Administración de los cursos',1),(8,'Docentes','Administración de maestros y docentes',1),(9,'Administrativos','Gestión de información del personal administrativo',1),(10,'Materias','Gestión de materias',1),(11,'Lectivo','Gestión de año lectivo',1);
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias` (
  `id_noticia` bigint NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `fecha_publicacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_noticia`),
  KEY `idx_fecha` (`fecha_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias`
--

LOCK TABLES `noticias` WRITE;
/*!40000 ALTER TABLE `noticias` DISABLE KEYS */;
/*!40000 ALTER TABLE `noticias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `padre`
--

DROP TABLE IF EXISTS `padre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `padre` (
  `id_padre` bigint NOT NULL AUTO_INCREMENT,
  `id_persona` bigint NOT NULL,
  `id_estudiante` bigint NOT NULL,
  `tipo_parentesco` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nacionalidad` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `estado_civil` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `profesion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `empresa_trabajo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `status` int DEFAULT '1',
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  PRIMARY KEY (`id_padre`),
  KEY `id_persona` (`id_persona`),
  KEY `id_estudiante` (`id_estudiante`),
  CONSTRAINT `padre_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`),
  CONSTRAINT `padre_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `padre`
--

LOCK TABLES `padre` WRITE;
/*!40000 ALTER TABLE `padre` DISABLE KEYS */;
/*!40000 ALTER TABLE `padre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paralelo`
--

DROP TABLE IF EXISTS `paralelo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paralelo` (
  `id_paralelo` bigint NOT NULL AUTO_INCREMENT,
  `tutor` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'Sin asignación',
  `nivel` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `grado` int NOT NULL,
  `sigla` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `cupo` int DEFAULT '30',
  `turno` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT 'Mañana',
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id_paralelo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paralelo`
--

LOCK TABLES `paralelo` WRITE;
/*!40000 ALTER TABLE `paralelo` DISABLE KEYS */;
INSERT INTO `paralelo` VALUES (1,'Sin asignación','Primaria',1,'A',30,'Mañana','2025-01-22 18:43:17',1),(2,'Sin asignación','Primaria',2,'A',30,'Mañana','2025-01-22 18:43:17',1),(3,'Sin asignación','Primaria',3,'A',30,'Mañana','2025-01-22 18:43:17',1),(4,'Sin asignación','Primaria',4,'A',30,'Mañana','2025-01-22 18:43:17',1),(5,'Sin asignación','Primaria',5,'A',30,'Mañana','2025-01-22 18:43:17',1),(6,'Vanesa Yujra Samora','Inicial',0,'A',30,'Mañana','2025-01-22 18:43:17',1),(7,'Sin asignación','Primaria',6,'A',30,'Mañana','2025-01-22 18:43:17',1);
/*!40000 ALTER TABLE `paralelo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pensiones`
--

DROP TABLE IF EXISTS `pensiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pensiones` (
  `id_pensiones` bigint NOT NULL AUTO_INCREMENT,
  `id_matricula` bigint DEFAULT NULL,
  `fecha_reg_pago` datetime DEFAULT NULL,
  `tipo_pago` enum('Efectivo','Transferencia','Deposito','Qr') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `ci` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `relacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `estado_pago` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `mes` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pensiones`),
  UNIQUE KEY `id_matricula` (`id_matricula`,`mes`),
  KEY `idx_pensiones_matricula_mes` (`id_matricula`,`mes`),
  CONSTRAINT `pensiones_ibfk_1` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pensiones`
--

LOCK TABLES `pensiones` WRITE;
/*!40000 ALTER TABLE `pensiones` DISABLE KEYS */;
INSERT INTO `pensiones` VALUES (1,32,'2024-10-10 00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,50.00,1,1,'Febrero','2024-11-25 09:48:30'),(2,32,'2024-02-02 00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,50.00,1,1,'Marzo','2024-11-25 09:48:30'),(3,32,'2024-11-25 00:00:00','Efectivo','465604','Rene','Vasquez','7268984','Estudiate',50.00,1,1,'Abril','2024-11-25 09:48:30'),(4,32,'2024-11-25 21:00:29','Efectivo','4465','Rene','Vasquez','7268984','Padre',50.00,1,1,'Mayo','2024-11-25 09:48:30'),(5,32,'2024-11-25 20:51:33','Efectivo','4465','Rene','Vasquez','7897','Lobo',50.00,1,1,'Junio','2024-11-25 09:48:30'),(6,32,'2024-11-25 20:49:52','Efectivo','1234','Rene','Vasquez','7268984','Lobo',50.00,1,1,'Julio','2024-11-25 09:48:30'),(7,32,'2024-11-25 20:43:39','Deposito','87898754','Rene','Vasquez','7268984','Padre',100.00,1,1,'Agosto','2024-11-25 09:48:30'),(8,32,'2024-11-25 20:04:14','Efectivo','77','Alejandro','Vasquez','7268984','Padre',70.00,1,1,'Septiembre','2024-11-25 09:48:30'),(9,32,'2024-11-26 00:27:58','Transferencia','87898754','Rene','Vasquez','7897','Padre',50.00,1,1,'Octubre','2024-11-25 09:48:30'),(10,32,'2024-11-25 21:00:47','Efectivo','4465','Rene','Vasquez','7897','Padre',50.00,1,1,'Noviembre','2024-11-25 09:48:30'),(11,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Febrero','2024-11-25 12:27:42'),(12,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Marzo','2024-11-25 12:27:42'),(13,33,'2024-11-26 01:23:44','Qr','4465','Rene','Vasquez','7897','Padre',50.00,1,1,'Abril','2024-11-25 12:27:42'),(14,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Mayo','2024-11-25 12:27:42'),(15,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Junio','2024-11-25 12:27:42'),(16,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Julio','2024-11-25 12:27:42'),(17,33,'2024-11-26 09:30:16','Efectivo','0','Patrona','Vare','4068841','Tia',50.00,1,1,'Agosto','2024-11-25 12:27:42'),(18,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Septiembre','2024-11-25 12:27:42'),(19,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Octubre','2024-11-25 12:27:42'),(20,33,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50.00,0,1,'Noviembre','2024-11-25 12:27:42'),(21,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:14:53'),(22,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:14:53'),(23,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:14:53'),(24,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:14:53'),(25,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:14:53'),(26,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:14:53'),(27,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:14:53'),(28,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:14:53'),(29,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:14:53'),(30,34,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:14:53'),(31,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:14:59'),(32,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:14:59'),(33,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:14:59'),(34,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:14:59'),(35,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:14:59'),(36,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:14:59'),(37,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:14:59'),(38,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:14:59'),(39,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:14:59'),(40,35,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:14:59'),(41,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:01'),(42,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:01'),(43,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:01'),(44,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:01'),(45,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:01'),(46,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:01'),(47,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:01'),(48,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:01'),(49,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:01'),(50,36,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:01'),(51,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:05'),(52,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:05'),(53,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:05'),(54,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:05'),(55,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:05'),(56,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:05'),(57,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:05'),(58,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:05'),(59,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:05'),(60,37,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:05'),(61,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:26'),(62,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:26'),(63,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:26'),(64,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:26'),(65,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:26'),(66,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:26'),(67,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:26'),(68,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:26'),(69,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:26'),(70,38,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:26'),(71,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:27'),(72,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:27'),(73,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:27'),(74,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:27'),(75,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:27'),(76,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:27'),(77,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:27'),(78,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:27'),(79,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:27'),(80,39,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:27'),(81,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:31'),(82,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:31'),(83,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:31'),(84,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:31'),(85,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:31'),(86,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:31'),(87,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:31'),(88,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:31'),(89,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:31'),(90,40,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:31'),(91,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:31'),(92,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:31'),(93,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:31'),(94,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:31'),(95,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:31'),(96,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:31'),(97,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:31'),(98,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:31'),(99,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:31'),(100,41,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:31'),(101,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:32'),(102,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:32'),(103,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:32'),(104,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:32'),(105,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:32'),(106,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:32'),(107,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:32'),(108,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:32'),(109,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:32'),(110,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:32'),(111,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:33'),(112,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:33'),(113,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:33'),(114,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:33'),(115,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:33'),(116,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:33'),(117,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:33'),(118,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:33'),(119,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:33'),(120,43,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:33'),(121,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:33'),(122,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:33'),(123,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:33'),(124,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:33'),(125,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:33'),(126,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:33'),(127,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:33'),(128,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:33'),(129,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:33'),(130,44,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:33'),(131,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:34'),(132,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:34'),(133,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:34'),(134,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:34'),(135,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:34'),(136,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:34'),(137,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:34'),(138,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:34'),(139,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:34'),(140,45,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:34'),(141,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:35'),(142,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:35'),(143,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:35'),(144,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:35'),(145,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:35'),(146,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:35'),(147,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:35'),(148,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:35'),(149,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:35'),(150,46,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:35'),(151,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:35'),(152,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:35'),(153,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:35'),(154,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:35'),(155,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:35'),(156,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:35'),(157,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:35'),(158,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:35'),(159,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:35'),(160,47,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:35'),(161,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:36'),(162,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:36'),(163,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:36'),(164,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:36'),(165,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:36'),(166,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:36'),(167,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:36'),(168,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:36'),(169,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:36'),(170,48,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:36'),(171,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:36'),(172,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:36'),(173,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:36'),(174,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:36'),(175,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:36'),(176,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:36'),(177,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:36'),(178,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:36'),(179,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:36'),(180,49,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:36'),(181,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:37'),(182,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:37'),(183,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:37'),(184,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:37'),(185,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:37'),(186,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:37'),(187,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:37'),(188,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:37'),(189,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:37'),(190,50,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:37'),(191,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:37'),(192,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:37'),(193,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:37'),(194,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:37'),(195,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:37'),(196,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:37'),(197,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:37'),(198,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:37'),(199,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:37'),(200,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:37'),(201,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:38'),(202,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:38'),(203,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:38'),(204,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:38'),(205,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:38'),(206,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:38'),(207,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:38'),(208,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:38'),(209,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:38'),(210,52,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:38'),(211,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:38'),(212,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:38'),(213,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:38'),(214,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:38'),(215,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:38'),(216,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:38'),(217,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:38'),(218,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:38'),(219,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:38'),(220,53,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:38'),(221,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:39'),(222,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:39'),(223,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:39'),(224,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:39'),(225,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:39'),(226,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:39'),(227,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:39'),(228,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:39'),(229,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:39'),(230,54,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:39'),(231,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:39'),(232,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:39'),(233,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:39'),(234,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:39'),(235,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:39'),(236,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:39'),(237,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:39'),(238,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:39'),(239,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:39'),(240,55,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:39'),(241,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:40'),(242,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:40'),(243,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:40'),(244,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:40'),(245,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:40'),(246,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:40'),(247,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:40'),(248,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:40'),(249,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:40'),(250,56,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:40'),(251,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:40'),(252,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:40'),(253,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:40'),(254,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:40'),(255,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:40'),(256,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:40'),(257,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:40'),(258,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:40'),(259,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:40'),(260,57,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:40'),(261,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:41'),(262,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:41'),(263,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:41'),(264,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:41'),(265,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:41'),(266,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:41'),(267,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:41'),(268,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:41'),(269,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:41'),(270,58,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:41'),(271,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:41'),(272,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:41'),(273,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:41'),(274,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:41'),(275,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:41'),(276,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:41'),(277,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:41'),(278,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:41'),(279,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:41'),(280,59,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:41'),(281,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:45'),(282,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:45'),(283,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:45'),(284,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:45'),(285,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:45'),(286,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:45'),(287,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:45'),(288,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:45'),(289,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:45'),(290,60,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:45'),(291,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:46'),(292,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:46'),(293,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:46'),(294,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:46'),(295,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:46'),(296,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:46'),(297,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:46'),(298,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:46'),(299,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:46'),(300,61,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:46'),(301,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:46'),(302,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:46'),(303,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:46'),(304,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:46'),(305,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:46'),(306,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:46'),(307,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:46'),(308,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:46'),(309,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:46'),(310,62,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:46'),(311,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Febrero','2024-11-26 18:15:48'),(312,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Marzo','2024-11-26 18:15:48'),(313,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Abril','2024-11-26 18:15:48'),(314,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Mayo','2024-11-26 18:15:48'),(315,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Junio','2024-11-26 18:15:48'),(316,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Julio','2024-11-26 18:15:48'),(317,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Agosto','2024-11-26 18:15:48'),(318,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Septiembre','2024-11-26 18:15:48'),(319,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Octubre','2024-11-26 18:15:48'),(320,63,NULL,NULL,NULL,NULL,NULL,NULL,NULL,25.00,0,1,'Noviembre','2024-11-26 18:15:48');
/*!40000 ALTER TABLE `pensiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `idpermiso` bigint NOT NULL AUTO_INCREMENT,
  `rolid` bigint NOT NULL,
  `moduloid` bigint NOT NULL,
  `r` int NOT NULL DEFAULT '0',
  `w` int NOT NULL DEFAULT '0',
  `u` int NOT NULL DEFAULT '0',
  `d` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idpermiso`),
  KEY `rolid` (`rolid`),
  KEY `moduloid` (`moduloid`)
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (171,3,1,1,0,0,0),(172,3,2,0,0,0,0),(173,3,3,1,0,0,0),(174,3,4,1,0,0,0),(175,3,5,1,0,0,0),(176,3,6,1,0,0,0),(177,3,7,1,0,0,0),(178,3,8,1,0,0,0),(179,3,9,1,0,0,0),(180,3,10,1,0,0,0),(181,3,11,1,0,0,0),(182,4,1,1,1,1,1),(183,4,2,0,0,0,0),(184,4,3,1,1,1,1),(185,4,4,1,1,1,1),(186,4,5,0,0,0,0),(187,4,6,0,0,0,0),(188,4,7,1,1,1,1),(189,4,8,1,1,1,1),(190,4,9,1,1,1,1),(191,4,10,0,0,0,0),(192,4,11,1,0,0,0),(193,5,1,1,1,1,1),(194,5,2,0,0,0,0),(195,5,3,1,0,0,0),(196,5,4,1,0,0,0),(197,5,5,0,0,0,0),(198,5,6,0,0,0,0),(199,5,7,1,0,0,0),(200,5,8,1,0,0,0),(201,5,9,1,0,0,0),(202,5,10,1,0,0,0),(203,5,11,1,0,0,0),(204,6,1,1,1,1,1),(205,6,2,0,0,0,0),(206,6,3,1,0,0,0),(207,6,4,1,0,0,0),(208,6,5,1,1,1,1),(209,6,6,1,1,1,1),(210,6,7,1,0,0,0),(211,6,8,1,0,0,0),(212,6,9,1,0,0,0),(213,6,10,0,0,0,0),(214,6,11,1,0,0,0),(226,1,1,1,1,1,1),(227,1,2,1,1,1,1),(228,1,3,1,1,1,1),(229,1,4,1,1,1,1),(230,1,5,1,1,1,1),(231,1,6,1,1,1,1),(232,1,7,1,1,1,1),(233,1,8,1,1,1,1),(234,1,9,1,1,1,1),(235,1,10,1,1,1,1),(236,1,11,1,1,1,1),(237,7,1,1,0,0,0),(238,7,2,0,0,0,0),(239,7,3,0,0,0,0),(240,7,4,0,0,0,0),(241,7,5,0,0,0,0),(242,7,6,0,0,0,0),(243,7,7,0,0,0,0),(244,7,8,0,0,0,0),(245,7,9,0,0,0,0),(246,7,10,0,0,0,0),(247,7,11,0,0,0,0),(248,2,1,1,1,1,1),(249,2,2,0,0,0,0),(250,2,3,1,0,0,0),(251,2,4,1,1,1,1),(252,2,5,1,0,0,0),(253,2,6,1,0,0,0),(254,2,7,1,1,1,1),(255,2,8,1,1,1,1),(256,2,9,1,1,1,1),(257,2,10,1,1,1,1),(258,2,11,1,1,1,1);
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `persona`
--

DROP TABLE IF EXISTS `persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `persona` (
  `id_persona` bigint NOT NULL AUTO_INCREMENT,
  `ci` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `sexo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `direccion_dom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `cel` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL,
  `status` int DEFAULT '2',
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `fecha_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_rol` bigint NOT NULL,
  PRIMARY KEY (`id_persona`),
  UNIQUE KEY `ci` (`ci`),
  UNIQUE KEY `cel` (`cel`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  KEY `idx_persona_nombre_apellido` (`nombre`,`apellido`),
  CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `persona`
--

LOCK TABLES `persona` WRITE;
/*!40000 ALTER TABLE `persona` DISABLE KEYS */;
INSERT INTO `persona` VALUES (1,'7268984','Rene Alejandro','Vasquez Vare','M','Av. Paragua 4º Anillo','67230415','reneravv1@gmail.com','admin','a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3',1,'','2024-11-22 18:21:44',1),(2,'76543210','Luis','Pérez','Masculino','Calle 1, Zona Centro','76543210','luis.perez@mail.com','lperez','7c6a180b36896a0a8c02787eeafb0e4c',1,NULL,'2024-11-22 18:24:49',7),(3,'76543211','Ana','Flores','Femenino','Calle 2, Zona Norte','76543211','ana.flores@mail.com','aflores','6cb75f652a9b52798eb6cf2201057c73',1,NULL,'2024-11-22 18:24:49',7),(4,'76543212','Carlos','Mendoza','M','Calle 3, Zona Sur','76543212','carlos.mendoza@mail.com','carlos.mendoza@mail.com','b58699806faeb31b3faaaae54cf574037c4087f0dd3e6505839d9094f66a0453',1,NULL,'2024-11-22 18:24:49',7),(5,'76543213','María','Gómez','F','Calle 4, Zona Este','76543213','maria.gomez@mail.com','maria.gomez@mail.com','8284ea114a2b39856a8d43832aeb2d16ed3a369799532fabdde522961097d119',1,NULL,'2024-11-22 18:24:49',7),(6,'76543214','Jorge','Villca','M','Calle 5, Zona Oeste','76543214','jorge.villca@mail.com','jorge.villca@mail.com','50ae6d4dd3c497b67df4ace3ee300783206fdfabc2a1e3f928dec95f3b549224',1,NULL,'2024-11-22 18:24:49',7),(7,'76543215','Carla','Sánchez','F','Calle 6, Zona Centro','76543215','carla.sanchez@mail.com','carla.sanchez@mail.com','829cb00db07c7b29d989863cd058880c82a382d28e2857799526ee71ee272838',1,NULL,'2024-11-22 18:24:49',7),(8,'76543216','José','López','Masculino','Calle 7, Zona Norte','76543216','jose.lopez@mail.com','jlopez','00cdb7bb942cf6b290ceb97d6aca64a3',1,NULL,'2024-11-22 18:24:49',7),(9,'76543217','Patricia','Vargas','Femenino','Calle 8, Zona Sur','76543217','patricia.vargas@mail.com','pvargas','b25ef06be3b6948c0bc431da46c2c738',1,NULL,'2024-11-22 18:24:49',7),(10,'76543218','Daniel','Jiménez','Masculino','Calle 9, Zona Este','76543218','daniel.jimenez@mail.com','djimenez','5d69dd95ac183c9643780ed7027d128a',1,NULL,'2024-11-22 18:24:49',7),(11,'76543219','Laura','Pérez','Femenino','Calle 10, Zona Oeste','76543219','laura.perez@mail.com','lperez2','87e897e3b54a405da144968b2ca19b45',1,NULL,'2024-11-22 18:24:49',7),(12,'76543220','Mario','Ríos','Masculino','Calle 11, Zona Centro','76543220','mario.rios@mail.com','mrios','1e5c2776cf544e213c3d279c40719643',1,NULL,'2024-11-22 18:24:49',7),(13,'76543221','Monica','Rojas','Femenino','Calle 12, Zona Norte','76543221','monica.rojas@mail.com','mrojas','c24a542f884e144451f9063b79e7994e',1,NULL,'2024-11-22 18:24:49',7),(14,'76543222','Juan','Rivera','Masculino','Calle 13, Zona Sur','76543222','juan.rivera@mail.com','jrivera','ee684912c7e588d03ccb40f17ed080c9',1,NULL,'2024-11-22 18:24:49',7),(15,'76543223','Mónica','Arias','Femenino','Calle 14, Zona Este','76543223','monica.arias@mail.com','marias','8ee736784ce419bd16554ed5677ff35b',1,NULL,'2024-11-22 18:24:49',7),(16,'76543224','Carlos','Salazar','Masculino','Calle 15, Zona Oeste','76543224','carlos.salazar@mail.com','csalazar','9141fea0574f83e190ab7479d516630d',1,NULL,'2024-11-22 18:24:49',7),(17,'76543225','Luis','Aguilar','Masculino','Calle 16, Zona Centro','76543225','luis.aguilar@mail.com','laguilar','2b40aaa979727c43411c305540bbed50',1,NULL,'2024-11-22 18:24:49',7),(18,'76543226','Sara','Arce','Femenino','Calle 17, Zona Norte','76543226','sara.arce@mail.com','sarce','a63f9709abc75bf8bd8f6e1ba9992573',1,NULL,'2024-11-22 18:24:49',7),(19,'76543227','Pedro','Quispe','Masculino','Calle 18, Zona Sur','76543227','pedro.quispe@mail.com','pquispe','80b8bdceb474b5127b6aca386bb8ce14',1,NULL,'2024-11-22 18:24:49',7),(20,'76543228','Ana','Vargas','Femenino','Calle 19, Zona Este','76543228','ana.vargas@mail.com','avargas','e532ae6f28f4c2be70b500d3d34724eb',1,NULL,'2024-11-22 18:24:49',7),(21,'76543229','Luis','Romero','Masculino','Calle 20, Zona Oeste','76543229','luis.romero@mail.com','lromero','aee67d9bb569ad1562f7b67cfccbd2ef',1,NULL,'2024-11-22 18:24:49',7),(22,'76543230','Laura','Suárez','Femenino','Calle 21, Zona Centro','76543230','laura.suarez@mail.com','lsuarez','568c31f0f2406ab70255a1d83291220f',1,NULL,'2024-11-22 18:24:49',7),(23,'76543231','Mario','Álvarez','Masculino','Calle 22, Zona Norte','76543231','mario.alvarez@mail.com','malvarez','069103d83d40b742a336dee5fb92f4e5',1,NULL,'2024-11-22 18:24:49',7),(24,'76543232','Luis','Ramos','Masculino','Calle 23, Zona Sur','76543232','luis.ramos@mail.com','lramos','1f82cdf9195b31244721c6026587fb78',1,NULL,'2024-11-22 18:24:49',7),(25,'76543233','Pedro','Vargas','Masculino','Calle 24, Zona Este','76543233','pedro.vargas@mail.com','pvargas2','58bad6b697dff48f4927941962f23e90',1,NULL,'2024-11-22 18:24:49',7),(26,'76543234','Ana','Quispe','Femenino','Calle 25, Zona Oeste','76543234','ana.quispe@mail.com','aquispe','6982e82c0b21af5526754d83df2d1635',1,NULL,'2024-11-22 18:24:49',7),(27,'76543235','Carlos','Fernández','Masculino','Calle 26, Zona Centro','76543235','carlos.fernandez@mail.com','cfernandez','dc2d937cba912f093445d008f0461c83',1,NULL,'2024-11-22 18:24:49',7),(28,'76543236','Laura','Rivera','Femenino','Calle 27, Zona Norte','76543236','laura.rivera@mail.com','lrivera','ccf08fd9a560b266470bf8ab97fc7c26',1,NULL,'2024-11-22 18:24:49',7),(29,'76543237','Luis','Vargas','Masculino','Calle 28, Zona Sur','76543237','luis.vargas@mail.com','lvargas','3b635d4df2c9ece93b97759531d6ed01',1,NULL,'2024-11-22 18:24:49',7),(30,'76543238','Patricia','López','Femenino','Calle 29, Zona Este','76543238','patricia.lopez@mail.com','plopez','926742e502de7d22686bb1d4a07fe635',1,NULL,'2024-11-22 18:24:49',7),(31,'76543239','Luis','Rojas','M','Calle 30, Zona Oeste','7654323','luis.rojas@mail.co','lrojas','3dc94727dbba08bdd21d7b318b410600',1,NULL,'2024-11-22 18:24:49',7),(32,'7268985','Rena','Vare','M','av. paragua','4465600','g@gmail.com','g@gmail.com','e9c156b63e3fd3cde784b07994617aeefc8faa8f8093536ea8f77fbe9fe57c33',1,NULL,'2024-11-22 18:34:49',7),(33,'101010','Leonel','Messi','M','Estados Unidos','1010','messi@gmail.com','messi@gmail.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4',1,NULL,'2024-11-23 19:59:47',7),(37,'101101','Angel','Dimaria','M','Argentina','7268888','dm@gmail.com','dm@gmail.com','a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3',1,NULL,'2024-11-23 20:07:24',7),(38,'4465604','Rene','Vasquez Cespedes','M','Calle España N66','63539315','renervc1@gmail.com','director','1d28c120568c10e19b9d8abe8b66d0983fa3d2e11ee7751aca50f83c6f4a43aa',1,NULL,'2024-11-26 11:00:33',2),(39,'8787','Juan','Perez Colpa','M','Av. Cristo Redentor','78787878','juan@gmail.com','juan@gmail.com','fd4d18552b5be40e9d2dd7615973c8ffe9aac393666efab859748c268ed5e49d',1,NULL,'2025-12-11 15:48:11',7);
/*!40000 ALTER TABLE `persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `idrol` bigint NOT NULL AUTO_INCREMENT,
  `nombrerol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador','Acceso a todo el sistema',1),(2,'Director','Supervisores',1),(3,'Coordinador','Coordinador',1),(4,'Secretario','Secretaria',1),(5,'Docente','Docente',1),(6,'Contador','Contador',1),(7,'Estudiante','Estudiante',1);
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonios`
--

DROP TABLE IF EXISTS `testimonios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonios` (
  `id_testimonio` bigint NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `testimonio` text NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_testimonio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonios`
--

LOCK TABLES `testimonios` WRITE;
/*!40000 ALTER TABLE `testimonios` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_mr_sistm'
--
/*!50003 DROP PROCEDURE IF EXISTS `get_gestion_activa` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_gestion_activa`()
begin 
select * from gestion where status = 1 limit 1;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `insertGestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertGestion`(
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ListarEstudiantesRegistrados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarEstudiantesRegistrados`()
BEGIN
    SELECT
        e.id_estudiante,
        e.id_persona,
        e.rude,
        p.nombre,
        p.apellido,
        e.estado_reg,
        p.email,
        p.cel,
        e.status AS status_estudiante
    FROM
        estudiante e
    JOIN persona p ON e.id_persona = p.id_persona where e.status !=0;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ListarInformacionEstudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ListarInformacionEstudiante`(IN estudiante_id BIGINT)
BEGIN
    
    SELECT
        p.ci,
        p.nombre,
        p.apellido,
        p.sexo,
        p.direccion_dom,
        p.cel,
        p.email,
        p.usuario,
        p.status AS persona_status,
        p.token,
        p.fecha_reg AS persona_fecha_reg,
        e.id_estudiante,
        e.colegio_proc,
        e.rude,
        e.provincia,
        e.ciudad,
        e.pais,
        e.fnacimiento,
        e.emergencia,
        e.estado_reg,
        e.fecha_reg AS estudiante_fecha_reg,
        e.status AS estudiante_status
    FROM
        persona p
    JOIN estudiante e ON p.id_persona = e.id_persona
    LEFT JOIN rol r ON p.id_rol = r.idrol
    WHERE e.id_estudiante = estudiante_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `listar_estudiantes_curso` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `listar_estudiantes_curso`(
    IN idParalelo BIGINT,
    IN gestion BIGINT
)
BEGIN
    
    SELECT 
    	estudiante.id_estudiante as id_estudiante,
        persona.ci AS ci_pers, 
        persona.nombre AS nombre_pers, 
        persona.apellido AS apellido_pers, 
        persona.email AS email_pers,
        persona.cel AS celular_pers, 
        persona.status AS status_estudiante, 
        matricula.gestion,
        paralelo.nivel,
        paralelo.grado, 
        paralelo.sigla AS paralelo, 
        paralelo.turno, 
        paralelo.status AS status_paralelo,
        matricula.id_matricula
    FROM 
        matricula
    JOIN 
        paralelo ON paralelo.id_paralelo = matricula.id_paralelo
    JOIN 
        estudiante ON estudiante.id_estudiante = matricula.id_estudiante
    JOIN 
        persona ON persona.id_persona = estudiante.id_persona
    WHERE 
        matricula.id_paralelo = idParalelo
        AND matricula.gestion = gestion
        AND matricula.status = true
    order by persona.apellido ASC;  
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `listar_matriculas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `listar_matriculas`()
BEGIN
    SELECT 
        m.id_matricula,
        m.gestion,
        m.tipo AS tipo_matricula,
        m.folio,
        m.estado_inscripcion,
        m.status AS estado_matricula,
        e.id_estudiante,
        p.ci AS ci_estudiante,
        p.nombre AS nombre_estudiante,
        p.apellido AS apellido_estudiante,
        pa.id_paralelo,
        CONCAT(pa.nivel, ' - ', pa.grado, pa.sigla) AS curso,
        pa.turno,
        pa.tutor,
        pa.cupo,
        pa.status AS estado_paralelo
    FROM 
        matricula m
    JOIN 
        estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN 
        persona p ON e.id_persona = p.id_persona
    JOIN 
        paralelo pa ON m.id_paralelo = pa.id_paralelo
    ORDER BY 
        m.gestion DESC, pa.nivel, pa.grado, pa.sigla, p.apellido, p.nombre;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `matricular_estudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `matricular_estudiante`(
    IN p_ci VARCHAR(30),
    IN p_gestion INT,
    IN p_id_paralelo BIGINT,
    IN p_tipo_matricula VARCHAR(20),
    IN p_folio TEXT,
    IN p_estado_inscripcion VARCHAR(25)
)
BEGIN
    DECLARE v_id_estudiante BIGINT;
    
    
    SELECT e.id_estudiante
    INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci and e.status=1 ;
    
    
    IF v_id_estudiante IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no existe en el sistema.';
    ELSE
        
        IF EXISTS (
            SELECT 1
            FROM matricula m
            WHERE m.id_estudiante = v_id_estudiante
            AND m.gestion = p_gestion
            AND m.id_paralelo = p_id_paralelo
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El estudiante ya está matriculado en esta gestión y paralelo.';
        ELSE
            
            INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
            VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);
        END IF;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `matricular_estudiante_y_generar_pensiones` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `matricular_estudiante_y_generar_pensiones`(
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
    DECLARE v_error_message TEXT;

    
    START TRANSACTION;

    
    SELECT e.id_estudiante
    INTO v_id_estudiante
    FROM estudiante e
    JOIN persona p ON p.id_persona = e.id_persona
    WHERE p.ci = p_ci AND e.status = 1;

    
    IF v_id_estudiante IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante no existe o no tiene un estado activo.';
    END IF;

    
    IF EXISTS (
        SELECT 1
        FROM matricula m
        WHERE m.id_estudiante = v_id_estudiante
        AND m.gestion = p_gestion
        AND m.id_paralelo = p_id_paralelo
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El estudiante ya está matriculado en esta gestión y paralelo.';
    END IF;

    
    INSERT INTO matricula (id_estudiante, id_paralelo, id_user, gestion, tipo, folio, estado_inscripcion, status)
    VALUES (v_id_estudiante, p_id_paralelo, 1, p_gestion, p_tipo_matricula, p_folio, p_estado_inscripcion, TRUE);

    SET v_id_matricula = LAST_INSERT_ID();

    IF v_id_matricula IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al insertar la matrícula.';
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

        SELECT monto_pension
        INTO v_monto_pension
        FROM gestion
        WHERE gestion = p_gestion AND status = 1
        LIMIT 1;

        INSERT INTO pensiones (id_matricula, fecha_reg_pago, tipo_pago, codigo, nombre, apellido, ci, relacion, monto, estado_pago, status, mes)
        VALUES (v_id_matricula, NULL, NULL, NULL, NULL, NULL, NULL, NULL, v_monto_pension, 0, 1, v_mes);

        IF ROW_COUNT() = 0 THEN
		    SET v_error_message = CONCAT('Error al insertar la pensión para el mes: ', v_mes); 
		    SIGNAL SQLSTATE '45000'
		    SET MESSAGE_TEXT = v_error_message; 
		END IF;

        SET v_mes_index = v_mes_index + 1;
    END WHILE;

    
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtenerGestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerGestion`(IN p_gestion BIGINT)
BEGIN
    SELECT    gestion, inicio, fin, gestion_l, monto_pension, descripcion, status
    FROM  gestion  WHERE  gestion = p_gestion;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtenerGestiones` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerGestiones`()
BEGIN
    SELECT * FROM gestion;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_colegio_por_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_colegio_por_id`(
    IN p_id_colegio BIGINT
)
BEGIN
    SELECT 
        id_colegio,
        nombre_col,
        codigo_registro_col,
        tipo_col,
        nivel_educativo_col,
        direccion_col,
        ciudad_col,
        telefono_col,
        correo_col,
        sitio_web_col,
        director_col,
        fecha_creacion_col,
        nro_estudiantes_col,
        nro_docentes_col,
        descripcion_col,
        horario_atencion_col,
        status
    FROM 
        colegio
    WHERE 
        id_colegio = p_id_colegio
    AND status = 1; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_detalle_completo_pension` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_detalle_completo_pension`(
    IN p_id_pension BIGINT
)
BEGIN
    SELECT 
        p.id_pensiones AS id_pension,
        p.mes AS mes_pension,
        p.monto AS monto_pagar,
        p.estado_pago AS estado_pension,
        p.tipo_pago,
        p.codigo,
        p.nombre AS pagador_nombre,
        p.apellido AS pagador_apellido,
        p.ci AS pagador_ci,
        p.relacion AS relacion_pagador,
        p.fecha_reg_pago AS fecha_pago,
        m.id_matricula,
        m.gestion,
        g.monto_pension AS monto_pension_gestion,
        m.tipo AS tipo_matricula,
        pa.nivel AS nivel_paralelo,
        pa.grado AS grado_paralelo,
        pa.sigla AS sigla_paralelo,
        pa.turno AS turno_paralelo,
        e.emergencia AS contacto_emergencia,
        pr.ci AS ci_estudiante,
        pr.usuario AS usuario_estudiante,
        pr.nombre AS nombre_estudiante,
        pr.apellido AS apellido_estudiante,
        pr.cel AS celular_estudiante,
        pr.email AS email_estudiante
    FROM 
        pensiones p
    INNER JOIN 
        matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN 
        gestion g ON m.gestion = g.gestion
    INNER JOIN 
        paralelo pa ON m.id_paralelo = pa.id_paralelo
    INNER JOIN 
        estudiante e ON m.id_estudiante = e.id_estudiante
    INNER JOIN 
        persona pr ON e.id_persona = pr.id_persona
    WHERE 
        p.id_pensiones = p_id_pension;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_detalle_pension` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_detalle_pension`(
    IN p_id_pension BIGINT
)
BEGIN
    SELECT 
        p.id_pensiones AS id_pension,
        p.mes AS mes_pension,
        p.monto AS monto_pagar,
        p.estado_pago AS estado_pension,
        p.tipo_pago,
        p.codigo,
        p.nombre AS pagador_nombre,
        p.apellido AS pagador_apellido,
        p.ci AS pagador_ci,
        p.relacion AS relacion_pagador,
        p.fecha_reg_pago AS fecha_pago,
        m.id_matricula,
        m.gestion,
        pr.ci AS estudiante_ci,
        pr.nombre AS estudiante_nombre,
        pr.apellido AS estudiante_apellido,
        pr.email AS estudiante_email
    FROM 
        pensiones p
    INNER JOIN 
        matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN 
        persona pr ON m.id_persona = pr.id_persona
    WHERE 
        p.id_pensiones = p_id_pension;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_informacion_pensiones_pagadas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_informacion_pensiones_pagadas`()
BEGIN
    SELECT 
    	pen.id_pensiones as id_pension,
        per.ci AS CI_Estudiante,
        m.id_matricula AS Matricula,
        per.nombre AS Nombre_Estudiante,
        per.apellido AS Apellido_Estudiante,
        CONCAT(par.nivel, ': ', par.grado, ' - ', par.sigla) AS Curso,
        pen.monto AS Monto_A_Pagar,
        pen.fecha_reg_pago as fecha_pago,
        pen.mes as mes_pago,
        m.gestion AS Gestion,
        pen.status AS status_Pension,
        CASE 
            WHEN pen.estado_pago = 1 THEN 'Pagado'
            ELSE 'Pendiente'
        END AS Estado_Pago
    FROM 
        pensiones pen
    JOIN matricula m ON pen.id_matricula = m.id_matricula
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN persona per ON e.id_persona = per.id_persona
    JOIN paralelo par ON m.id_paralelo = par.id_paralelo
    WHERE 
        pen.status = 1 and pen.estado_pago=1
    ORDER BY  fecha_reg_pago ASC; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_informacion_pensiones_Todas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_informacion_pensiones_Todas`()
BEGIN
    SELECT 
        per.ci AS CI_Estudiante,
        m.id_matricula AS Matricula,
        per.nombre AS Nombre_Estudiante,
        per.apellido AS Apellido_Estudiante,
        CONCAT(par.nivel, ': ', par.grado, ' - ', par.sigla) AS Curso,
        pen.monto AS Monto_A_Pagar,
        m.gestion AS Gestion,
        pen.status AS status_Pension,
        CASE 
            WHEN pen.estado_pago = 1 THEN 'Pagado'
            ELSE 'Pendiente'
        END AS Estado_Pago
    FROM 
        pensiones pen
    JOIN matricula m ON pen.id_matricula = m.id_matricula
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    JOIN persona per ON e.id_persona = per.id_persona
    JOIN paralelo par ON m.id_paralelo = par.id_paralelo
    WHERE 
        pen.status = 1 ; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `obtener_pensiones_estudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_pensiones_estudiante`(
    IN p_ci VARCHAR(20)
)
BEGIN
    SELECT 
        p.id_pensiones,
        e.id_persona,
        m.id_matricula AS matricula,
        per.ci AS ci_estudiante,
        CONCAT(per.nombre, ' ', per.apellido) AS nombre_completo,
        CONCAT(pa.nivel, ': ', pa.grado,' - ',pa.sigla) AS curso,
        p.monto AS monto_a_pagar,
        p.estado_pago,
        m.gestion AS gestion_academica,
        p.mes AS mes_pago
    FROM
        pensiones p
    INNER JOIN
        matricula m ON p.id_matricula = m.id_matricula
    INNER JOIN
        estudiante e ON m.id_estudiante = e.id_estudiante
    INNER JOIN
        persona per ON e.id_persona = per.id_persona
    INNER JOIN
        paralelo pa ON m.id_paralelo = pa.id_paralelo
    WHERE
        per.ci = p_ci
    ORDER BY
        m.gestion DESC, pa.grado ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `pagarPension` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `pagarPension`(
    IN p_id_pension INT,
    IN p_tipo_pago VARCHAR(50),
    IN p_codigo VARCHAR(20),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(100),
    IN p_ci VARCHAR(20),
    IN p_relacion VARCHAR(50),
    IN p_monto DECIMAL(10, 2),
    in p_fecha datetime
)
BEGIN
    
    DECLARE v_id_matricula INT;
    DECLARE v_estado VARCHAR(50);

    
    SELECT id_matricula, status
    INTO v_id_matricula, v_estado
    FROM pensiones
    WHERE id_pensiones = p_id_pension;

    
    IF v_id_matricula IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La pensión no existe';
    END IF;

    
    UPDATE pensiones
    SET
        tipo_pago = p_tipo_pago,
        codigo = p_codigo,
        nombre = p_nombre,
        apellido = p_apellido,
        ci = p_ci,
        relacion = p_relacion,
        monto = p_monto,
        estado_pago = 1,
        fecha_reg_pago = p_fecha
    WHERE id_pensiones = p_id_pension;

    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `paralelo_por_gestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `paralelo_por_gestion`(
	in p_paralelo BIGINT, 
    IN p_gestion BIGINT 
)
BEGIN
    SELECT 
        p.id_paralelo,
        p.nivel,
        p.grado,
        p.sigla,
        p.cupo,
        p.fecha_reg,
        p.tutor,
        p.turno,
        p.status AS status_paralelo,
        COUNT(m.id_estudiante) AS total_inscritos
    FROM 
        paralelo p
    LEFT JOIN 
        matricula m ON p.id_paralelo = m.id_paralelo
    WHERE 
        (m.gestion = p_gestion OR m.gestion IS NULL)and p.id_paralelo = p_paralelo 
  ;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_materia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_materia`(
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
        AND id_materia != p_id_materia 
        AND status != 0;

    
    IF NOT materia_existe THEN
        
        IF EXISTS (SELECT 1 FROM materia WHERE id_materia = p_id_materia) THEN
            
            UPDATE materia
            SET 
                area_mat = p_area_mat,
                nombre_mat = p_nombre_mat,
                descripcion_mat = p_descripcion_mat,
                grado = p_grado,
                nivel = p_nivel,
                horas_mat = p_horas_mat,
                status = p_status
            WHERE id_materia = p_id_materia;
        ELSE
            
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'La materia no existe';
        END IF;
    ELSE
        
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Ya existe una materia con el mismo nombre y grado';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_close_active_gestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_close_active_gestion`()
BEGIN
    
    IF EXISTS (SELECT 1 FROM gestion WHERE status = 1) THEN
        
        UPDATE gestion
        SET status = 2
        WHERE status = 1;
    ELSE
        
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'No existe ninguna gestión activa para cerrar.';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_contar_inscritos_paralelo_por_gestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_contar_inscritos_paralelo_por_gestion`(
    IN p_gestion BIGINT 
)
BEGIN
    SELECT 
        p.id_paralelo,
        p.nivel,
        p.grado,
        p.sigla,
        p.cupo,
        p.fecha_reg,
        p.tutor,
        p.turno,
        p.status AS status_paralelo,
        COUNT(m.id_estudiante) AS total_inscritos
    FROM 
        paralelo p
    LEFT JOIN 
        matricula m ON p.id_paralelo = m.id_paralelo
    WHERE 
        (m.gestion = p_gestion OR m.gestion IS NULL) 
    GROUP BY 
        p.id_paralelo, p.nivel, p.grado, p.sigla, p.cupo, p.fecha_reg, p.status
    ORDER BY 
        p.nivel, p.grado, p.sigla;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_guardar_o_actualizar_persona` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_o_actualizar_persona`( IN p_id_persona BIGINT,    IN p_identificacion VARCHAR(30),    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(200),    IN p_sexo VARCHAR(10),    IN p_direccion_dom TEXT,    IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100),    IN p_usuario VARCHAR(100), IN p_password TEXT,    IN p_status INT,    IN p_id_rol BIGINT
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    
    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE (email = p_email OR ci = p_identificacion) 
          AND id_persona != p_id_persona 
          AND status != 0;

    
    IF persona_existe THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'el Ci o Email ya existen';
    ELSE
        
        IF p_id_persona IS NOT NULL AND p_id_persona > 0 THEN
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
        else
         
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se ha proporcionado Id_persona. Error';
        END IF;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_materia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_materia`(    IN p_area_mat VARCHAR(50),
    IN p_nombre_mat VARCHAR(50),    IN p_descripcion_mat TEXT,
    IN p_grado INT,    IN p_nivel VARCHAR(10),
    IN p_horas_mat INT,    IN p_status INT
)
BEGIN
    DECLARE resultado BOOLEAN DEFAULT FALSE;
	
    
    IF EXISTS (
        SELECT 1 FROM materia 
        WHERE nombre_mat = p_nombre_mat AND grado = p_grado
    ) THEN
        
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'La materia ya existe en este grado';
    END IF;
    
	
    INSERT INTO materia(area_mat, nombre_mat, descripcion_mat, grado, nivel, horas_mat, status)
    VALUES(p_area_mat, p_nombre_mat, p_descripcion_mat, p_grado, p_nivel, p_horas_mat, p_status);
   
    SET resultado = TRUE;
	
	    
	    SELECT resultado AS exito;
	END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_o_actualizar_materia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_o_actualizar_materia`(    IN p_id_materia BIGINT,    IN p_area_mat VARCHAR(50),    IN p_nombre_mat VARCHAR(50),
    IN p_descripcion_mat TEXT,    IN p_grado INT,    IN p_nivel VARCHAR(10),    IN p_horas_mat INT,    IN p_status INT)
BEGIN
    DECLARE materia_existe BOOLEAN DEFAULT FALSE;

    
    SELECT COUNT(*) > 0 INTO materia_existe
    FROM materia
    WHERE nombre_mat = p_nombre_mat AND grado = p_grado 
    AND (p_id_materia IS NULL OR id_materia != p_id_materia) AND status != 0;

    
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_insertar_persona` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insertar_persona`(
    IN p_identificacion VARCHAR(30),
    IN p_nombre VARCHAR(100),
    IN p_apellido VARCHAR(200),
    IN p_sexo VARCHAR(10),
    IN p_direccion_dom text,
    IN p_cel VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_usuario VARCHAR(100),
    IN p_password TEXT,
    IN p_status int,
    IN p_id_rol BIGINT
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    
    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE email = p_email AND ci = p_identificacion AND status != 0;

    
    IF NOT persona_existe THEN
        INSERT INTO persona(ci, nombre, apellido, sexo, direccion_dom, cel, email, usuario, password, status, id_rol) 
        VALUES(p_identificacion, p_nombre, p_apellido, p_sexo, p_direccion_dom, p_cel, p_email, p_usuario, p_password, p_status, p_id_rol);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La persona ya existe';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_limpiar_logs_antiguos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_limpiar_logs_antiguos`(
    IN p_dias INT
)
BEGIN
    DELETE FROM login_intentos
    WHERE fecha_intento < DATE_SUB(NOW(), INTERVAL p_dias DAY);
    
    SELECT ROW_COUNT() AS registros_eliminados;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_registrar_estudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_estudiante`(
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
    in p_statusGeneral int
)
BEGIN
    DECLARE v_id_persona BIGINT;
    DECLARE v_error_message VARCHAR(255);

    
    START TRANSACTION;

    
    IF EXISTS (SELECT 1 FROM `persona` WHERE `ci` = p_ci) THEN
        SET v_error_message = CONCAT('Error: El CI "', p_ci, '" ya está registrado.');
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
    END IF;

    IF EXISTS (SELECT 1 FROM `persona` WHERE `cel` = p_cel) THEN
        SET v_error_message = CONCAT('Error: El número de celular "', p_cel, '" ya está registrado.');
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
    END IF;

    IF EXISTS (SELECT 1 FROM `persona` WHERE `email` = p_email) THEN
        SET v_error_message = CONCAT('Error: El correo electrónico "', p_email, '" ya está registrado.');
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
    END IF;

    
    INSERT INTO `persona` (
        `ci`, `nombre`, `apellido`, `sexo`, `direccion_dom`, `cel`, `email`, 
        `usuario`, `password`, `id_rol`, `fecha_reg`,status 
    ) VALUES (
        p_ci, p_nombre, p_apellido, p_sexo, p_direccion_dom, p_cel, p_email, 
        p_usuario, p_password, p_id_rol, NOW(),p_statusGeneral
    );

    
    SET v_id_persona = LAST_INSERT_ID();

    
    IF EXISTS (SELECT 1 FROM `estudiante` WHERE `rude` = p_rude) THEN
        SET v_error_message = CONCAT('Error: El RUDE "', p_rude, '" ya está registrado.');
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_message;
    END IF;

    
    INSERT INTO `estudiante` (
        `id_persona`, `colegio_proc`, `rude`, `provincia`, `ciudad`, `pais`, 
        `fnacimiento`, `emergencia`, `estado_reg`, `fecha_reg`,status
    ) VALUES (
        v_id_persona, p_colegio_proc, p_rude, p_provincia, p_ciudad, p_pais, 
        p_fnacimiento, p_emergencia, p_estado_reg, NOW(),p_statusGeneral
    );

    
    COMMIT;

    
    SELECT 'Estudiante registrado exitosamente.' AS mensaje;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_registrar_intento_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_intento_login`(
    IN p_usuario VARCHAR(100),
    IN p_exitoso TINYINT(1),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(255)
)
BEGIN
    INSERT INTO login_intentos (usuario, exitoso, ip_address, user_agent)
    VALUES (p_usuario, p_exitoso, p_ip_address, p_user_agent);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_select_personas_con_rol` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_select_personas_con_rol`(IN esAdmin BOOLEAN)
BEGIN
    SELECT 
        p.id_persona,
        p.ci,
        p.nombre,
        p.apellido,
        p.cel,
        p.email,
        p.status,
        r.idrol,
        r.nombrerol
    FROM 
        persona p
    INNER JOIN 
        rol r ON p.id_rol = r.idrol
    WHERE 
        p.status != 0
    AND 
        (esAdmin = FALSE OR p.id_persona != 1);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_select_persona_rol` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_select_persona_rol`(in p_persona bigint)
begin
		
	SELECT 
	    p.id_persona,
	    p.ci,
	    p.nombre,
	    p.apellido,
	    p.cel,
	    p.email,
	    p.direccion_dom,
	    p.sexo,
	    p.usuario,
	    DATE_FORMAT(p.fecha_reg, '%d-%m-%Y') AS fecha_reg,  
	    r.idrol,
	    r.nombrerol,
	    p.status 
	FROM persona p
	INNER JOIN rol r ON p.id_rol = r.idrol
	WHERE p.id_persona = p_persona;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_validar_persona_exist` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_validar_persona_exist`(
    IN p_email VARCHAR(100),
    IN p_ci VARCHAR(100)
)
BEGIN
    DECLARE persona_existe BOOLEAN DEFAULT FALSE;

    SELECT COUNT(*) > 0 INTO persona_existe
    FROM persona
    WHERE email = p_email AND ci = p_ci AND status != 0;

    SELECT persona_existe AS existe;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_verificar_bloqueo_login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_verificar_bloqueo_login`(
    IN p_usuario VARCHAR(100),
    IN p_minutos INT,
    IN p_max_intentos INT,
    OUT p_bloqueado TINYINT(1)
)
BEGIN
    DECLARE v_intentos INT;
    
    -- Contar intentos fallidos en la ventana de tiempo
    SELECT COUNT(*) INTO v_intentos
    FROM login_intentos
    WHERE usuario = p_usuario
    AND exitoso = 0
    AND fecha_intento > DATE_SUB(NOW(), INTERVAL p_minutos MINUTE);
    
    -- Determinar si está bloqueado
    IF v_intentos >= p_max_intentos THEN
        SET p_bloqueado = 1;
    ELSE
        SET p_bloqueado = 0;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `test_signal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_signal`()
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Mensaje de error personalizado';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `updateEstudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateEstudiante`(
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
    FROM estudiante
    WHERE id_estudiante = p_idEstudiante;

    IF v_idPersona IS NOT NULL THEN
        
        SELECT COUNT(*) INTO v_rudeExists
        FROM estudiante
        WHERE rude = p_rude AND id_estudiante != p_idEstudiante;

        IF v_rudeExists > 0 THEN
            
            ROLLBACK;
            SELECT 'Error: RUDE ya registrado para otro estudiante.' AS mensaje;
        ELSE
            
            UPDATE persona
            SET 
                ci = p_ci,
                nombre = p_nombre,
                apellido = p_apellido,
                sexo = p_sexo,
                cel = p_telefono,
                email = p_email,
                direccion_dom = p_direccion,
                id_rol = p_tipoId,
                password = p_password,
                usuario = p_usuario
            WHERE id_persona = v_idPersona;

            
            UPDATE estudiante
            SET 
                rude = p_rude,
                estado_reg = p_estadoReg,
                colegio_proc = p_colegioProc,
                provincia = p_provincia,
                ciudad = p_ciudad,
                pais = p_pais,
                fnacimiento = p_fnacimiento,
                status = p_status,
                emergencia = p_emergencia
            WHERE id_estudiante = p_idEstudiante;

            
            COMMIT;
            SELECT 'Estudiante y persona actualizados correctamente.' AS mensaje;
        END IF;
    ELSE
        
        ROLLBACK;
        SELECT 'Error: Estudiante no encontrado.' AS mensaje;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `updateGestion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateGestion`(
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
    FROM gestion
    WHERE gestion = p_gestion;

    IF gestion_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La gestión que intenta actualizar no existe.';
    ELSE
        
        UPDATE gestion
        SET
            inicio = p_inicio,
            fin = p_fin,
            gestion_l = p_gestion_l,
            monto_pension = p_monto_pension,
            descripcion = p_descripcion
        WHERE gestion = p_gestion;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-13 14:51:45
