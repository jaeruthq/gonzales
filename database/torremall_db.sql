-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-09-2019 a las 17:44:02
-- Versión del servidor: 10.1.34-MariaDB
-- Versión de PHP: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `torremall_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobros`
--

CREATE TABLE `cobros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `salida_id` int(10) UNSIGNED NOT NULL,
  `vehiculo_id` bigint(20) UNSIGNED NOT NULL,
  `tarifa_id` bigint(20) UNSIGNED NOT NULL,
  `tiempo_cobrado` int(11) NOT NULL,
  `fecha_reg` date NOT NULL,
  `hora` time NOT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `total` decimal(24,2) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_usuarios`
--

CREATE TABLE `datos_usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apep` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apem` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `datos_usuarios`
--

INSERT INTO `datos_usuarios` (`id`, `nom`, `apep`, `apem`, `ci`, `ci_exp`, `dir`, `email`, `fono`, `cel`, `foto`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'JHONNY', 'CARVAJAL', 'MAMANI', '12345678', 'LP', 'ZONA LOS OLIVOS CALLE 4 #156', '', '232367', '78994612', '155144470121002JHONNY.jpg', 2, '2019-08-19 14:55:14', '2019-09-01 22:23:32'),
(2, 'PATRCIA', 'CONDORI', '', '12345678', 'LP', 'ZONA LOS PEDREGALES AV. LOS OLIVOS #456', '', '', '7845612', 'PCONDORIPATRCIA1566227328.jpg', 4, '2019-08-19 15:03:03', '2019-08-19 15:08:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cod` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_aut` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_emp` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dpto` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ciudad` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zona` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calle` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fono` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fax` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `casilla` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actividad_eco` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `cod`, `nit`, `nro_aut`, `nro_emp`, `name`, `alias`, `pais`, `dpto`, `ciudad`, `zona`, `calle`, `nro`, `email`, `fono`, `cel`, `fax`, `casilla`, `web`, `logo`, `actividad_eco`, `created_at`, `updated_at`) VALUES
(1, 'EMP01', '1231564564', '2315674898', '6666544555', 'TORREMALL', 'E.P.', 'BOLIVIA', 'LA PAZ', 'LA PAZ', 'LOS OLIVOS', 'LOS HEROES', '233', '', '2316489', '68465315', '', '', '', 'TORREMALL_1567034944.png', 'CON FINES DE LUCRO', '2019-08-19 14:55:14', '2019-08-28 23:29:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cobro_id` bigint(20) UNSIGNED NOT NULL,
  `a_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_factura` bigint(20) NOT NULL,
  `codigo_control` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(24,2) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `fecha_emision` date NOT NULL,
  `estado` int(11) NOT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_salidas`
--

CREATE TABLE `ingreso_salidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehiculo_id` int(10) UNSIGNED NOT NULL,
  `accion` enum('INGRESO','SALIDA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hora` time NOT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_reg` date NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mapeos`
--

CREATE TABLE `mapeos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion_id` bigint(20) UNSIGNED NOT NULL,
  `ocupado` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mapeos`
--

INSERT INTO `mapeos` (`id`, `nom`, `ubicacion_id`, `ocupado`, `vehiculo_id`, `created_at`, `updated_at`) VALUES
(1, 'A', 1, 0, 0, '2019-08-28 16:17:36', '2019-08-30 15:18:52'),
(2, 'B', 1, 0, 0, '2019-08-28 16:17:36', '2019-08-30 16:56:54'),
(3, 'C', 1, 0, 0, '2019-08-28 16:17:36', '2019-08-30 17:33:50'),
(4, 'D', 1, 0, 0, '2019-08-28 16:17:36', '2019-09-02 13:29:45'),
(5, 'E', 1, 0, 0, '2019-08-28 16:17:36', '2019-08-28 16:17:36'),
(6, 'F', 1, 0, 0, '2019-08-28 16:17:36', '2019-08-28 16:17:36'),
(7, 'G', 1, 0, 0, '2019-08-28 16:17:36', '2019-09-02 13:25:43'),
(8, 'H', 1, 0, 0, '2019-08-28 16:17:36', '2019-09-02 13:33:34'),
(9, 'I', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(10, 'J', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(11, 'K', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(12, 'L', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(13, 'M', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(14, 'N', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(15, 'O', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(16, 'P', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(17, 'Q', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(18, 'R', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(19, 'S', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(20, 'T', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(21, 'U', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(22, 'V', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(23, 'W', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(24, 'X', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(25, 'Y', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(26, 'Z', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(27, 'A-1', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(28, 'B-2', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(29, 'C-3', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(30, 'D-4', 1, 0, 0, '2019-08-28 16:17:37', '2019-08-28 16:17:37'),
(31, 'A', 2, 0, 0, '2019-08-30 20:44:15', '2019-08-30 20:44:15'),
(32, 'B', 2, 0, 0, '2019-08-30 20:44:15', '2019-08-30 20:44:15'),
(33, 'C', 2, 0, 0, '2019-08-30 20:44:15', '2019-08-30 20:44:15'),
(34, 'D', 2, 0, 0, '2019-08-30 20:44:15', '2019-08-30 20:44:15'),
(35, 'E', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(36, 'F', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(37, 'G', 2, 1, 1, '2019-08-30 20:44:16', '2019-08-30 20:45:28'),
(38, 'H', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(39, 'I', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(40, 'J', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(41, 'K', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(42, 'L', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(43, 'M', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(44, 'N', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(45, 'O', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(46, 'P', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(47, 'Q', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(48, 'R', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(49, 'S', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(50, 'T', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(51, 'U', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(52, 'V', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(53, 'W', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(54, 'X', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(55, 'Y', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(56, 'Z', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(57, 'A-1', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(58, 'B-2', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(59, 'C-3', 2, 0, 0, '2019-08-30 20:44:16', '2019-08-30 20:44:16'),
(60, 'D-4', 2, 0, 0, '2019-08-30 20:44:17', '2019-08-30 20:44:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_04_08_140849_create_datos_usuarios_table', 1),
(4, '2019_04_08_150906_create_empresas_table', 1),
(5, '2019_08_19_100050_create_ubicacions_table', 1),
(6, '2019_08_19_100205_create_tarifas_table', 1),
(7, '2019_08_19_100217_create_propietarios_table', 1),
(8, '2019_08_19_100218_create_tipo_vehiculos_table', 1),
(9, '2019_08_19_100219_create_vehiculos_table', 1),
(10, '2019_08_19_100314_create_ingreso_salidas_table', 1),
(11, '2019_08_19_100350_create_notificacion_usuarios_table', 1),
(12, '2019_08_19_103301_create_mapeos_table', 1),
(13, '2019_08_19_103302_create_cobros_table', 1),
(14, '2019_08_19_103303_create_facturas_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_usuarios`
--

CREATE TABLE `notificacion_usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ingresoSalida_id` bigint(20) UNSIGNED NOT NULL,
  `hora` time NOT NULL,
  `fecha` date NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `visto` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

CREATE TABLE `propietarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apep` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apem` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_exp` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fono` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_reg` date NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `propietarios`
--

INSERT INTO `propietarios` (`id`, `nom`, `apep`, `apem`, `ci`, `ci_exp`, `dir`, `fono`, `cel`, `correo`, `foto`, `fecha_reg`, `status`, `created_at`, `updated_at`) VALUES
(1, 'JUAN', 'PACHECO', '', '12345678', 'LP', 'ZONA LOS PEDREGALES AV. LOS OLIVOS #456', '2134567', '7845612', 'JUAN@GMAIL.COM', 'PACHECOJUANJUAN1566310267.jpg', '2019-08-20', 0, '2019-08-20 14:11:07', '2019-08-20 14:14:43'),
(2, 'PEDRO', 'GONZALES', 'MAMANI', '12345678', 'CB', 'ZONA LOS PEDREGALES AV. LOS OLIVOS #456', '2134568', '78945636', 'PEDRO@GMAIL.COM', 'GONZALESPEDRO1566313296.jpg', '2019-08-20', 1, '2019-08-20 14:13:33', '2019-08-20 15:01:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas`
--

CREATE TABLE `salidas` (
  `id` int(10) UNSIGNED NOT NULL,
  `salida_id` int(11) UNSIGNED NOT NULL,
  `cobrado` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

CREATE TABLE `tarifas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `horas` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `fecha_reg` date NOT NULL,
  `descripcion` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id`, `nom`, `horas`, `precio`, `fecha_reg`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'TARIFA 1', '1', '5.00', '2019-08-20', '', '2019-08-20 14:47:05', '2019-08-20 14:47:05'),
(2, 'TARIFA 2', '4', '18.00', '2019-08-20', '', '2019-08-20 14:47:20', '2019-08-20 14:47:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_vehiculos`
--

CREATE TABLE `tipo_vehiculos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_vehiculos`
--

INSERT INTO `tipo_vehiculos` (`id`, `nom`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'TIPO 1', '', '2019-08-20 13:49:11', '2019-08-20 13:50:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacions`
--

CREATE TABLE `ubicacions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int(11) NOT NULL,
  `descripcion` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacions`
--

INSERT INTO `ubicacions` (`id`, `nom`, `capacidad`, `descripcion`, `status`, `created_at`, `updated_at`) VALUES
(1, 'SECCION 1', 30, '', 1, '2019-08-28 16:17:36', '2019-08-28 17:30:56'),
(2, 'SECCION 2', 30, '', 1, '2019-08-30 20:44:15', '2019-08-30 20:44:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('ADMINISTRADOR','AUXILIAR','CONTROL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `tipo`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$5vSkoNPnUTsIWCy5u7zk/u.Ne.BEhDB2yI7zWY72yHpECrj9/Owpi', 'ADMINISTRADOR', 'user_default.png', 1, '2019-08-19 14:55:14', '2019-08-19 14:55:14'),
(2, 'JCARVAJAL', '$2y$10$z683vcZK/7OP7QnMUQD9M.uNRhI11S.6axp.PNEwrwUUbTngbZLku', 'AUXILIAR', 'user_default.png', 1, '2019-08-19 14:55:14', '2019-08-19 14:55:14'),
(4, 'PCONDORI', '$2y$10$/qwe4Z3gGanitSbCypo2ROhspiO3TufpMWSn3l7dp483a76eEppGa', 'AUXILIAR', 'user_default.png', 1, '2019-08-19 15:03:03', '2019-08-19 15:06:59'),
(11, 'CONTROL1', '$2y$10$uMA5QZkuOoFZjiHFSZQvmeSOdh4PjgSF4Jhb5szjJ4IKTyC.6PAe2', 'CONTROL', 'user_default.png', 1, '2019-09-01 22:17:46', '2019-09-01 22:30:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `placa` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_id` bigint(20) UNSIGNED NOT NULL,
  `propietario_id` bigint(20) UNSIGNED NOT NULL,
  `tarifa_id` bigint(20) UNSIGNED NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_reg` varchar(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `placa`, `marca`, `modelo`, `nom`, `tipo_id`, `propietario_id`, `tarifa_id`, `foto`, `rfid`, `fecha_reg`, `status`, `created_at`, `updated_at`) VALUES
(1, '1569-ASD', 'TOYOTA', '2005', 'VEHICULO 1', 1, 2, 1, 'VEHICULO_11564789456101566312740.jpg', '156478945610', '2019-08-20', 1, '2019-08-20 14:52:20', '2019-08-20 14:54:00'),
(2, '4520-QWE', 'NISSAN', '2007', 'VEHICULO 2', 1, 2, 2, 'VEHICULO_24520-QWE1566313391.jpg', '156478945611', '2019-08-20', 1, '2019-08-20 14:57:58', '2019-08-28 20:54:07');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cobros`
--
ALTER TABLE `cobros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cobros_vehiculo_id_foreign` (`vehiculo_id`),
  ADD KEY `cobros_tarifa_id_foreign` (`tarifa_id`);

--
-- Indices de la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `datos_usuarios_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facturas_cobro_id_foreign` (`cobro_id`);

--
-- Indices de la tabla `ingreso_salidas`
--
ALTER TABLE `ingreso_salidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mapeos`
--
ALTER TABLE `mapeos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mapeos_ubicacion_id_foreign` (`ubicacion_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificacion_usuarios`
--
ALTER TABLE `notificacion_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notificacion_usuarios_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_vehiculos`
--
ALTER TABLE `tipo_vehiculos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicacions`
--
ALTER TABLE `ubicacions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehiculos_rfid_unique` (`rfid`),
  ADD KEY `vehiculos_tipo_id_foreign` (`tipo_id`),
  ADD KEY `vehiculos_propietario_id_foreign` (`propietario_id`),
  ADD KEY `vehiculos_tarifa_id_foreign` (`tarifa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cobros`
--
ALTER TABLE `cobros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ingreso_salidas`
--
ALTER TABLE `ingreso_salidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `mapeos`
--
ALTER TABLE `mapeos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `notificacion_usuarios`
--
ALTER TABLE `notificacion_usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `salidas`
--
ALTER TABLE `salidas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_vehiculos`
--
ALTER TABLE `tipo_vehiculos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ubicacions`
--
ALTER TABLE `ubicacions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cobros`
--
ALTER TABLE `cobros`
  ADD CONSTRAINT `cobros_tarifa_id_foreign` FOREIGN KEY (`tarifa_id`) REFERENCES `tarifas` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `cobros_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `datos_usuarios`
--
ALTER TABLE `datos_usuarios`
  ADD CONSTRAINT `datos_usuarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_cobro_id_foreign` FOREIGN KEY (`cobro_id`) REFERENCES `cobros` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `mapeos`
--
ALTER TABLE `mapeos`
  ADD CONSTRAINT `mapeos_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicacions` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `notificacion_usuarios`
--
ALTER TABLE `notificacion_usuarios`
  ADD CONSTRAINT `notificacion_usuarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_propietario_id_foreign` FOREIGN KEY (`propietario_id`) REFERENCES `propietarios` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `vehiculos_tarifa_id_foreign` FOREIGN KEY (`tarifa_id`) REFERENCES `tarifas` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `vehiculos_tipo_id_foreign` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_vehiculos` (`id`) ON DELETE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
