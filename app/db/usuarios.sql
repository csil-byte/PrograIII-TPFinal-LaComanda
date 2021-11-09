-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2021 a las 00:47:01
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--
DROP TABLE IF EXISTS `empleado`;
CREATE TABLE IF NOT EXISTS `empleado` (
  `id_empleado` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `clave` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre_empleado` varchar(50) NOT NULL,
  `cantidad_operaciones` int(11) DEFAULT '0',
  PRIMARY KEY (`id_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `usuario`, `clave`, `id_tipo`, `nombre_empleado`, `cantidad_operaciones` ) VALUES
(1, 'francoP', 'Hsu23sDsjseWs', 4,  'Franco'),
(2, 'pedroF', 'dasdqsdw2sd23', 1, 'Pedro'),
(3, 'jorgeL', 'sda2s2f332f2', 2,  'Jorge'),
(4, 'lauraN', 'jdfj3442', 3, 'Laura');


--
-- Estructura de tabla para la tabla `sector`
--
DROP TABLE IF EXISTS `sector`;
CREATE TABLE IF NOT EXISTS `sector` (
  `id_sector` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_sector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 'sector' ('id_sector', 'nombre') VALUES
(1, 'Barra'),
(2, 'Choperia'),
(3, 'Cocina'),
(4, 'Candy Bar');


--
-- Estructura de tabla para la tabla `producto`
--
DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `precio` int(11) NOT NULL,
  `sector` int(11) NOT NULL,
  `tiempo_preparacion` int(11) DEFAULT '1',
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO 'producto' ('id_producto', 'nombre','precio','tipo','tiempo_preparacion') VALUES
(1, 'Pasta', 800,3, 25),
(2, 'Pizza', 1000,3, 30),
(3, 'Hamburguesa', 500,3, 20),
(4, 'Ensalada', 600,3, 15),
(5, 'Pollo', 700,3, 25),
(6, 'Pescado', 900,3, 30),
(7, 'Coca-Cola', 100,1, 5),
(8, 'Fanta', 100,1, 5),
(9, 'Sprite', 100,1, 5),
(10, 'Agua', 100,1, 5),
(11, 'Cerveza', 100,2, 5),
(12, 'Gin Tonic', 100,1, 5),
(13, 'Carne al horno', 1250, 3, 30),
(14, 'Margarita', 450, 1, 5),
(15, 'Cheesecake', 350, 4, 5),
(16, 'Flan', 290, 4, 5);


--
-- Estructura de tabla para la tabla `mesa`
--
DROP TABLE IF EXISTS `mesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesa` (
  `id_mesa` int(11) NOT NULL AUTO_INCREMENT,
  `estado_mesa` int(11) NOT NULL,
  `foto` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_mesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `mesa` VALUES 
('M1',1, NULL),
('M2',2,NULL),
('M3',3, NULL);

--
-- Estructura de tabla para la tabla `mesa`
--
DROP TABLE IF EXISTS `estados_mesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estados_mesa` (
  `id_estado_mesa` int(11) NOT NULL AUTO_INCREMENT,
  `estado_mesa` int(11) NOT NULL,
  PRIMARY KEY (`id_estado_mesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `estados_mesa` VALUES 
(1, 'Cliente esperando'),
(2, 'Cliente comiendo'),
(3, 'Cliente pagando'),
(4, 'Mesa cerrada');

--
-- Estructura de tabla para la tabla `pedido`
--
DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE 'pedido' (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `estado_pedido` int(11) NOT NULL,
  `hora_inicial` time NOT NULL,
  `hora_entrega_estimada` time DEFAULT NULL,
  `hora_entrega_real` time DEFAULT NULL,
  'minutos_estimados' int (11) NOT NULL,
  'id_mozo' int (11) NOT NULL,
  'id_cocinero' int (11) NOT NULL,
  PRIMARY KEY (`id_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Estructura de tabla para la tabla `estados pedido`
--
DROP TABLE IF EXISTS `estados_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estados_pedido` (
  `id_estado_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `estado_pedido` int(11) NOT NULL,
  PRIMARY KEY (`id_estado_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `estados_pedido` VALUES 
(1, 'En preparacion'),
(2, 'Listo para servir');

--
-- Estructura de tabla para la tabla `estados pedido`
--
DROP TABLE IF EXISTS `encuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuesta` (
  `id_encuesta` int(11) NOT NULL AUTO_INCREMENT,
  `rating_mesa` int(11) NOT NULL,
  `rating_restaurante` int(11) NOT NULL,
  `rating_mozo` int(11) NOT NULL,
  `rating_cocinero` int(11) NOT NULL,
  PRIMARY KEY (`id_estado_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;