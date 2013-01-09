CREATE DATABASE  IF NOT EXISTS `dev_dbkm` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `dev_dbkm`;
-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dev_dbkm
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.10.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acceso`
--

DROP TABLE IF EXISTS `acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del acceso',
  `usuario_id` int(11) NOT NULL COMMENT 'Identificador del usuario que accede',
  `tipo_acceso` int(1) NOT NULL DEFAULT '1' COMMENT 'Tipo de acceso (entrata o salida)',
  `ip` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del usuario que ingresa',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro del acceso',
  PRIMARY KEY (`id`),
  KEY `fk_acceso_usuario_idx` (`usuario_id`),
  CONSTRAINT `fk_acceso_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla que registra los accesos de los usuarios al sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acceso`
--

LOCK TABLES `acceso` WRITE;
/*!40000 ALTER TABLE `acceso` DISABLE KEYS */;
/*!40000 ALTER TABLE `acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `denominacion` varchar(200) NOT NULL,
  `tamano` varchar(45) DEFAULT NULL,
  `archivo` varchar(45) NOT NULL,
  `registrado_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_backup_usuario_idx` (`usuario_id`),
  CONSTRAINT `fk_backup_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene las copias de seguridad del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--

LOCK TABLES `backup` WRITE;
/*!40000 ALTER TABLE `backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudad`
--

DROP TABLE IF EXISTS `ciudad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ciudad` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de la ciudad',
  `ciudad` varchar(45) NOT NULL COMMENT 'Nombre de la cuidad',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de la última modificación',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudad`
--

LOCK TABLES `ciudad` WRITE;
/*!40000 ALTER TABLE `ciudad` DISABLE KEYS */;
INSERT INTO `ciudad` VALUES (1,'OCAÑA','2013-01-01 00:00:01',NULL);
/*!40000 ALTER TABLE `ciudad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador de la empresa',
  `razon_social` varchar(100) NOT NULL COMMENT 'Nombre de la empresa',
  `siglas` varchar(45) DEFAULT NULL COMMENT 'Siglas del nombre de la empresa',
  `nit` varchar(15) NOT NULL COMMENT 'Número de identificación tributaria de la empresa',
  `dv` int(2) DEFAULT NULL COMMENT 'Digito de verificación del NIT',
  `representante_legal` varchar(100) NOT NULL COMMENT 'Nombre del representante legal de la empresa',
  `nuip` bigint(20) NOT NULL COMMENT 'Número de identificación personal',
  `tipo_nuip_id` int(1) NOT NULL COMMENT 'Tipo de identificación',
  `pagina_web` varchar(45) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `registrado_at` varchar(45) DEFAULT NULL,
  `modificado_in` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_empresa_tipo_nuip_idx` (`tipo_nuip_id`),
  CONSTRAINT `fk_empresa_tipo_nuip` FOREIGN KEY (`tipo_nuip_id`) REFERENCES `tipo_nuip` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (1,'Nombre de la Empresa','Empresa LTDA','1.091.652.165',6,'IVAN DAVID MELENDEZ',1091652165,1,'dailyscript.com.co',NULL,'2013-01-01 00:00:01',NULL);
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_usuario`
--

DROP TABLE IF EXISTS `estado_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estado_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del estado del usuario',
  `usuario_id` int(11) NOT NULL COMMENT 'Identificador del usuario',
  `estado_usuario` int(11) NOT NULL COMMENT 'Código del estado del usuario',
  `descripcion` varchar(100) NOT NULL COMMENT 'Motivo del cambio de estado',
  `fecha_estado_at` datetime DEFAULT NULL COMMENT 'Fecha del cambio de estado',
  PRIMARY KEY (`id`),
  KEY `fk_estado_usuario_usuario_idx` (`usuario_id`),
  CONSTRAINT `fk_estado_usuario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los estados de los usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_usuario`
--

LOCK TABLES `estado_usuario` WRITE;
/*!40000 ALTER TABLE `estado_usuario` DISABLE KEYS */;
INSERT INTO `estado_usuario` VALUES (1,1,1,'BLOQUEADO POR SER UN USUARIO SIN PRIVILEGIOS','2013-01-01 00:00:01'),(2,2,2,'ACTIVO POR SER EL SUPER USUARIO DEL SISTEMA','2013-01-01 00:00:01');
/*!40000 ALTER TABLE `estado_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del menú',
  `menu_id` int(11) DEFAULT NULL COMMENT 'Identificador del menú padre',
  `recurso_id` int(11) DEFAULT NULL COMMENT 'Identificador del recurso',
  `menu` varchar(45) NOT NULL COMMENT 'Texto a mostrar del menú',
  `url` varchar(60) DEFAULT NULL COMMENT 'Url del menú',
  `posicion` int(11) DEFAULT '0' COMMENT 'Posisión dentro de otros items',
  `icono` varchar(45) DEFAULT NULL COMMENT 'Icono a mostrar ',
  `activo` int(1) NOT NULL DEFAULT '1' COMMENT 'Menú activo o inactivo',
  `visible` int(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el menú es visible o no',
  PRIMARY KEY (`id`),
  KEY `fk_menu_recurso_idx` (`recurso_id`),
  KEY `fk_menu_menu_idx` (`menu_id`),
  CONSTRAINT `fk_menu_recurso` FOREIGN KEY (`recurso_id`) REFERENCES `recurso` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_menu_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los menú para los usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,NULL,NULL,'Principal','principal',10,'icon-home',1,1),(2,NULL,NULL,'Configuraciones','#',80,'icon-wrench',1,1),(3,NULL,NULL,'Sistema','#',90,'icon-cogs',1,1),(4,1,NULL,'Inicio','principal',11,'icon-home',1,1),(5,2,NULL,'Perfiles','sistema/perfil/listar/',11,'icon-group',1,1),(6,2,NULL,'Recursos','sistema/recurso/listar/',12,'icon-lock',1,1),(7,2,NULL,'Menús','sistema/menu/listar/',13,'icon-list',1,1),(8,3,NULL,'Usuarios','sistema/usuario/listar/',11,'icon-user',1,1);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfil` (
  `id` int(2) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del perfil',
  `perfil` varchar(45) NOT NULL COMMENT 'Nombre del perfil',
  `activo` int(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el perfil esta activo o inactivo',
  `plantilla` varchar(45) DEFAULT 'default' COMMENT 'Plantilla para usar en el sitema',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro del perfil',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los grupos de los usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil`
--

LOCK TABLES `perfil` WRITE;
/*!40000 ALTER TABLE `perfil` DISABLE KEYS */;
INSERT INTO `perfil` VALUES (1,'SUPER USUARIO',1,NULL,'2013-01-01 00:00:01');
/*!40000 ALTER TABLE `perfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `persona`
--

DROP TABLE IF EXISTS `persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persona` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `nuip` bigint(20) NOT NULL COMMENT 'Número de identificación personal',
  `tipo_nuip_id` int(11) NOT NULL COMMENT 'Tipo de identificación',
  `registrado_at` datetime DEFAULT NULL,
  `modificado_in` datetime DEFAULT NULL,
  `fotografia` varchar(45) DEFAULT NULL COMMENT 'Fotografía',
  PRIMARY KEY (`id`),
  KEY `fk_persona_tipo_nuip1_idx` (`tipo_nuip_id`),
  CONSTRAINT `fk_persona_tipo_nuip1` FOREIGN KEY (`tipo_nuip_id`) REFERENCES `tipo_nuip` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene las personas que interactúan con el sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `persona`
--

LOCK TABLES `persona` WRITE;
/*!40000 ALTER TABLE `persona` DISABLE KEYS */;
INSERT INTO `persona` VALUES (1,'ERROR','ERROR',1010101010,1,'2013-01-01 00:00:01',NULL,NULL),(2,'IVAN DAVID','MELENDEZ',1091652165,1,'2013-01-01 00:00:01',NULL,NULL);
/*!40000 ALTER TABLE `persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurso`
--

DROP TABLE IF EXISTS `recurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurso` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del recurso',
  `modulo` varchar(45) DEFAULT NULL COMMENT 'Nombre del módulo',
  `controlador` varchar(45) DEFAULT NULL COMMENT 'Nombre del controlador',
  `accion` varchar(45) DEFAULT NULL COMMENT 'Nombre de la acción',
  `recurso` varchar(100) DEFAULT NULL COMMENT 'Nombre del recurso',
  `descripcion` text NOT NULL COMMENT 'Descripción del recurso',
  `activo` int(1) NOT NULL DEFAULT '1' COMMENT 'Estado del recurso',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los recursos a los que acceden los usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurso`
--

LOCK TABLES `recurso` WRITE;
/*!40000 ALTER TABLE `recurso` DISABLE KEYS */;
INSERT INTO `recurso` VALUES (1,'NULL','principal',NULL,'/principal/','Página principal del sistema',1,'2013-01-01 00:00:01'),(2,'*','NULL','NULL','*','Comodín para dar recurso a todos los módulos del sistema',1,'2013-01-01 00:00:01');
/*!40000 ALTER TABLE `recurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurso_perfil`
--

DROP TABLE IF EXISTS `recurso_perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurso_perfil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recurso_id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `registrado_at` datetime DEFAULT NULL,
  `modificado_in` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_recurso_perfil_recurso_idx` (`recurso_id`),
  KEY `fk_recurso_perfil_perfil_idx` (`perfil_id`),
  CONSTRAINT `fk_recurso_perfil_recurso` FOREIGN KEY (`recurso_id`) REFERENCES `recurso` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_recurso_perfil_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los recursos del usuario en el sistema segun su perfl';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurso_perfil`
--

LOCK TABLES `recurso_perfil` WRITE;
/*!40000 ALTER TABLE `recurso_perfil` DISABLE KEYS */;
INSERT INTO `recurso_perfil` VALUES (1,2,1,'2013-01-01 00:00:01',NULL);
/*!40000 ALTER TABLE `recurso_perfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursal`
--

DROP TABLE IF EXISTS `sucursal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificación de la sucursal',
  `empresa_id` int(11) NOT NULL COMMENT 'Identificador de la empresa',
  `sucursal` varchar(45) NOT NULL COMMENT 'Nombre de la sucursal',
  `sucursal_slug` varchar(45) DEFAULT NULL COMMENT 'Slug de la sucursal',
  `direccion` varchar(45) DEFAULT NULL COMMENT 'Dirección de la sucursal',
  `telefono` varchar(45) DEFAULT NULL COMMENT 'Número del teléfono',
  `fax` varchar(45) DEFAULT NULL COMMENT 'Número del fax',
  `celular` varchar(45) DEFAULT NULL COMMENT 'Número de celular',
  `ciudad_id` int(11) NOT NULL COMMENT 'Identificador de la ciudad',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de la última modificación',
  PRIMARY KEY (`id`),
  KEY `fk_sucursal_empresa_idx` (`empresa_id`),
  KEY `fk_sucursal_ciudad_idx` (`ciudad_id`),
  CONSTRAINT `fk_sucursal_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_sucursal_ciudad` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudad` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursal`
--

LOCK TABLES `sucursal` WRITE;
/*!40000 ALTER TABLE `sucursal` DISABLE KEYS */;
INSERT INTO `sucursal` VALUES (1,1,'OFICINA PRINCIPAL','oficina-principal','Dirección','3162404183','3162404183','3162404183',1,'2013-01-01 00:00:01',NULL);
/*!40000 ALTER TABLE `sucursal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_nuip`
--

DROP TABLE IF EXISTS `tipo_nuip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_nuip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_nuip` varchar(45) NOT NULL COMMENT 'Nombre del tipo de identificación',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_nuip`
--

LOCK TABLES `tipo_nuip` WRITE;
/*!40000 ALTER TABLE `tipo_nuip` DISABLE KEYS */;
INSERT INTO `tipo_nuip` VALUES (1,'C.C.'),(2,'C.E.'),(3,'PAS.'),(4,'T.I.'),(5,'N.D.');
/*!40000 ALTER TABLE `tipo_nuip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del usuario',
  `sucursal_id` int(11) DEFAULT NULL COMMENT 'Identificador a la sucursal a la cual pertenece',
  `persona_id` int(11) NOT NULL COMMENT 'Identificador de la persona',
  `login` varchar(45) NOT NULL COMMENT 'Nombre de usuario',
  `password` varchar(45) NOT NULL COMMENT 'Contraseña de acceso al sistea',
  `perfil_id` int(2) NOT NULL COMMENT 'Identificador del perfil',
  `tema` varchar(45) DEFAULT 'default' COMMENT 'Tema aplicable para la interfaz',
  `app_ajax` int(1) DEFAULT '1' COMMENT 'Indica si la app se trabaja con ajax o peticiones normales',
  `registrado_at` datetime DEFAULT NULL COMMENT 'Fecha de registro',
  `modificado_in` datetime DEFAULT NULL COMMENT 'Fecha de la última modificación',
  PRIMARY KEY (`id`),
  KEY `fk_usuario_perfil_idx` (`perfil_id`),
  KEY `fk_usuario_persona_idx` (`persona_id`),
  KEY `fk_usuario_sucursal_idx` (`sucursal_id`),
  CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_usuario_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_usuario_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursal` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene los usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,NULL,1,'error','963db57a0088931e0e3627b1e73e6eb5',1,'default',1,'2013-01-01 00:00:01',NULL),(2,NULL,2,'admin','d93a5def7511da3d0f2d171d9c344e91',1,'default',1,'2013-01-01 00:00:01',NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-08 18:21:35