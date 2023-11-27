-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-11-2023 a las 19:18:03
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `parcial`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajuste`
--

CREATE TABLE `ajuste` (
  `id` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `idDeAjuste` int(11) DEFAULT NULL,
  `operacionAAjustar` varchar(255) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ajuste`
--

INSERT INTO `ajuste` (`id`, `motivo`, `idDeAjuste`, `operacionAAjustar`, `monto`) VALUES
(1, 'equivocacion al depositar', 10, 'deposito', 50.00),
(2, 'equivocacion al depositar', 10, 'deposito', 500.00),
(3, 'equivocacion al depositar', 10, 'deposito', -500.00),
(4, 'equivocacion al depositar', 10, 'deposito', -500.00),
(5, 'equivocacion al depositar', 10, 'deposito', 500.00),
(6, 'equivocacion al depositar', 10, 'deposito', 500.00),
(7, 'equivocacion al depositar', 10, 'deposito', -500.00),
(8, 'equivocacion al depositar', 10, 'deposito', -500.00),
(9, 'equivocacion al retirar', 10, 'retiro', 500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentabanco`
--

CREATE TABLE `cuentabanco` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `tipoDocumento` varchar(255) NOT NULL,
  `numeroDocumento` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipoDeCuenta` varchar(255) NOT NULL,
  `moneda` varchar(255) NOT NULL,
  `saldoInicial` int(11) NOT NULL,
  `nroDeCuenta` int(11) NOT NULL,
  `fotoDePerfil` varchar(255) DEFAULT NULL,
  `activo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentabanco`
--

INSERT INTO `cuentabanco` (`id`, `nombre`, `apellido`, `tipoDocumento`, `numeroDocumento`, `email`, `tipoDeCuenta`, `moneda`, `saldoInicial`, `nroDeCuenta`, `fotoDePerfil`, `activo`) VALUES
(1, 'Hernan', 'Sanchez', 'DNI', 12345674, 'hernan@gmail.com', 'CC$', '$', 111000, 737342, '737342CC$', 1),
(2, 'Pepito', 'Rodriguez', 'DNI', 77777777, 'pepito@gmail.com', 'CA$', '$', 34400, 937722, '937722CA$', 1),
(3, 'Fernando', 'Rodriguez', 'DNI', 11111111, 'fernando@gmail.com', 'CCU$S', 'U$S', 1538850, 448844, '448844CCU$S', 1),
(4, 'Alicia', 'Pagani', 'DNI', 44444444, 'alicia@gmail.com', 'CA$', '$', 1605800, 545533, '545533CA$', 1),
(5, 'Ricardo', 'Guatierrez', 'DNI', 33333333, 'ricardo@gmail.com', 'CC$', '$', 50000, 313908, '313908CC$', 1),
(6, 'Ricardo', 'Guatierrez', 'DNI', 44444444, 'ricardo@gmail.com', 'CA$', '$', 50000, 404747, '404747CA$', 1),
(7, 'Cuenta', 'Paraborrar', 'DNI', 11111114, 'ricardo@gmail.com', 'CA$', '$', 131200, 166732, '166732CA$', 1),
(8, 'Enzo', 'Fernandez', 'DNI', 48759622, 'enzo@gmail.com', 'CA$', '$', 10009, 778020, '778020CA$', 1),
(38, 'Gustavo', 'Rodrigueze', 'DNI', 22222222, 'pepito@gmail.com', 'CC$', '$', 9999, 67768, '67768CC$', 0),
(41, 'Cuenta3', 'Paraborrarrrrraaaa', 'DNI', 22222222, 'Cuenta2@gmail.com', 'CC$', '$', 500, 31144, '031144CC$', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deposito`
--

CREATE TABLE `deposito` (
  `id` int(11) NOT NULL,
  `idCuenta` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT NULL,
  `imagenTalonario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deposito`
--

INSERT INTO `deposito` (`id`, `idCuenta`, `fecha`, `importe`, `imagenTalonario`) VALUES
(1, '4', '2023-11-26', 1500000.00, 'CA5455331'),
(2, '4', '2023-11-26', 1500000.00, 'CA5455332'),
(3, '3', '2023-11-26', 1500000.00, 'CC4488443'),
(4, '1', '2023-11-26', 45000.00, 'CC7373424'),
(5, '1', '2023-11-26', 45000.00, 'CC7373425'),
(6, '7', '2023-11-26', 45000.00, 'CA1667326'),
(7, '7', '2023-11-26', 8300.00, 'CA1667327'),
(8, '7', '2023-11-26', 8300.00, 'CA1667328'),
(9, '7', '2023-11-26', 9999.00, 'CA1667329'),
(10, '7', '2023-11-26', 9449.00, 'CA$16673210'),
(11, '38', '2023-11-27', 9999.00, 'CC$06776811'),
(12, '8', '2023-11-27', 9999.00, 'Ca$77802012');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retiro`
--

CREATE TABLE `retiro` (
  `id` int(11) NOT NULL,
  `idCuenta` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `importe` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `retiro`
--

INSERT INTO `retiro` (`id`, `idCuenta`, `fecha`, `importe`) VALUES
(1, 1, '2023-11-27 11:40:53', 1000.00),
(2, 1, '2023-11-27 11:41:25', 1000.00),
(3, 1, '2023-11-27 11:41:35', 1000.00),
(4, 1, '2023-11-27 11:41:56', 1000.00),
(5, 1, '2023-11-27 11:44:38', 1000.00),
(6, 3, '2023-11-27 12:08:05', 1000.00),
(7, 3, '2023-11-27 12:08:07', 1000.00),
(8, 3, '2023-11-27 12:08:08', 1000.00),
(9, 2, '2023-11-27 12:08:43', 1000.00),
(10, 2, '2023-11-27 12:08:44', 1450.00),
(11, 2, '2023-11-27 19:12:33', 2000.00),
(12, 2, '2023-11-27 19:14:23', 2000.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajuste`
--
ALTER TABLE `ajuste`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentabanco`
--
ALTER TABLE `cuentabanco`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `deposito`
--
ALTER TABLE `deposito`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `retiro`
--
ALTER TABLE `retiro`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ajuste`
--
ALTER TABLE `ajuste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cuentabanco`
--
ALTER TABLE `cuentabanco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `deposito`
--
ALTER TABLE `deposito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `retiro`
--
ALTER TABLE `retiro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
