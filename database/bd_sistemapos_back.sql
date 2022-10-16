-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-10-2022 a las 04:11:19
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_sistemapos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Sony', '(Sin Descripción)', '2022-09-20 23:56:06', '2022-09-20 23:56:06'),
(2, 'Philips', '(Sin Descripción)', '2022-09-20 23:56:15', '2022-09-20 23:56:15'),
(3, 'JBL', '(Sin Descripción)', '2022-09-20 23:56:23', '2022-09-20 23:56:23'),
(4, 'XIAOMI', '(Sin Descripción)', '2022-09-20 23:56:41', '2022-09-20 23:56:41'),
(5, 'Microsoft Games', '(Sin Descripción)', '2022-09-20 23:56:52', '2022-09-20 23:56:52'),
(6, 'Nintendo', '(Sin Descripción)', '2022-09-20 23:57:01', '2022-09-20 23:57:01'),
(7, 'Marshall', '(Sin Descripción)', '2022-09-20 23:57:13', '2022-09-20 23:57:13'),
(8, 'CASIO', '(Sin Descripción)', '2022-09-20 23:57:32', '2022-09-20 23:57:32'),
(9, 'HEWLETT PACKARD (HP)', '(Sin Descripción)', '2022-09-20 23:58:24', '2022-09-20 23:58:24'),
(10, 'Samsung', '(Sin Descripción)', '2022-09-20 23:58:33', '2022-09-20 23:58:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Fotografía', '(Sin Descripción)', '2022-09-20 23:42:19', '2022-09-20 23:43:53'),
(2, 'Seguridad Y Vigilancia', '(Sin Descripción)', '2022-09-20 23:42:27', '2022-09-20 23:43:58'),
(3, 'Audio', '(Sin Descripción)', '2022-09-20 23:42:35', '2022-09-20 23:44:05'),
(4, 'Dispositivos Audivisuales', '(Sin Descripción)', '2022-09-20 23:42:42', '2022-09-20 23:44:10'),
(5, 'Consolas de Videojuegos', '(Sin Descripción)', '2022-09-20 23:42:51', '2022-09-20 23:44:13'),
(6, 'Almacenamiento', '(Sin Descripción)', '2022-09-20 23:42:56', '2022-09-20 23:44:43'),
(7, 'Proyectores de Video', '(Sin Descripción)', '2022-09-20 23:43:01', '2022-09-20 23:44:48'),
(8, 'Computadoras y Componentes', '(Sin Descripción)', '2022-09-20 23:43:07', '2022-09-20 23:44:29'),
(9, 'Laptops', '(Sin Descripción)', '2022-09-20 23:43:15', '2022-09-20 23:44:25'),
(10, 'Redes', '(Sin Descripción)', '2022-09-20 23:43:25', '2022-09-20 23:44:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `name`, `ci`, `nit`, `address`, `phone`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Cliente 1', '7689091', NULL, 'Alto Lima 1ra sección, calle Tiquina #6360', '65192834', NULL, '2022-09-21 00:49:53', '2022-09-21 00:49:53'),
(2, 'Cliente 2', '7698123', '12435467981', NULL, '65789012', NULL, '2022-09-21 00:50:20', '2022-09-21 00:50:20'),
(3, 'Cliente 3', '7698129', NULL, NULL, '21345687', NULL, '2022-09-21 03:34:26', '2022-10-07 03:54:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historydatas`
--

CREATE TABLE `historydatas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `month_date` date NOT NULL,
  `total_bs` decimal(8,2) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historydatas`
--

INSERT INTO `historydatas` (`id`, `month_date`, `total_bs`, `category_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, '2014-01-01', '9424.00', 10, 80, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(2, '2014-02-01', '4388.00', 1, 79, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(3, '2014-03-01', '10558.00', 4, 67, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(4, '2014-04-01', '1625.00', 10, 60, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(5, '2014-05-01', '7632.00', 10, 50, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(6, '2014-06-01', '8102.00', 1, 55, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(7, '2014-07-01', '14433.00', 1, 56, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(8, '2014-08-01', '5885.00', 3, 80, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(9, '2014-09-01', '7488.00', 1, 98, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(10, '2014-10-01', '3474.00', 4, 109, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(11, '2014-11-01', '2363.00', 8, 120, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(12, '2014-12-01', '6126.00', 5, 100, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(13, '2015-01-01', '6494.00', 9, 80, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(14, '2015-02-01', '1490.00', 1, 59, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(15, '2015-03-01', '7819.00', 8, 50, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(16, '2015-04-01', '9925.00', 4, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(17, '2015-05-01', '14760.00', 2, 38, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(18, '2015-06-01', '12156.00', 7, 49, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(19, '2015-07-01', '4292.00', 2, 67, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(20, '2015-08-01', '7161.00', 9, 54, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(21, '2015-09-01', '11947.00', 7, 78, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(22, '2015-10-01', '1116.00', 6, 89, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(23, '2015-11-01', '5259.00', 2, 95, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(24, '2015-12-01', '2136.00', 9, 115, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(25, '2016-01-01', '1633.00', 10, 95, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(26, '2016-02-01', '7873.00', 8, 52, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(27, '2016-03-01', '1421.00', 7, 45, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(28, '2016-04-01', '2354.00', 6, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(29, '2016-05-01', '1342.00', 9, 30, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(30, '2016-06-01', '1421.00', 3, 49, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(31, '2016-07-01', '15125.00', 7, 36, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(32, '2016-08-01', '1251.00', 3, 46, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(33, '2016-09-01', '154.00', 9, 55, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(34, '2016-10-01', '21321.00', 1, 56, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(35, '2016-11-01', '4123.00', 8, 96, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(36, '2016-12-01', '435.00', 5, 94, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(37, '2017-01-01', '7867.00', 8, 96, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(38, '2017-02-01', '25235.00', 5, 50, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(39, '2017-03-01', '5235.00', 9, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(40, '2017-04-01', '4535.00', 3, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(41, '2017-05-01', '6345.00', 5, 56, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(42, '2017-06-01', '4353.00', 9, 36, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(43, '2017-07-01', '8756.00', 3, 30, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(44, '2017-08-01', '3211.00', 7, 30, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(45, '2017-09-01', '657.00', 1, 35, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(46, '2017-10-01', '452.00', 1, 85, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(47, '2017-11-01', '2452.00', 2, 85, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(48, '2017-12-01', '452.00', 5, 120, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(49, '2018-01-01', '452.00', 4, 74, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(50, '2018-02-01', '4554.00', 7, 63, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(51, '2018-03-01', '8656.00', 2, 43, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(52, '2018-04-01', '9000.00', 6, 42, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(53, '2018-05-01', '6860.00', 2, 75, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(54, '2018-06-01', '1962.00', 5, 68, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(55, '2018-07-01', '5064.00', 1, 42, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(56, '2018-08-01', '9166.00', 2, 62, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(57, '2018-09-01', '33268.00', 9, 95, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(58, '2018-10-01', '452.00', 7, 100, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(59, '2018-11-01', '452.00', 4, 150, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(60, '2018-12-01', '4554.00', 8, 148, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(61, '2019-01-01', '8656.00', 9, 90, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(62, '2019-02-01', '12758.00', 1, 80, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(63, '2019-03-01', '16860.00', 3, 85, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(64, '2019-04-01', '20962.00', 7, 82, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(65, '2019-05-01', '33268.00', 3, 74, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(66, '2019-06-01', '452.00', 9, 28, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(67, '2019-07-01', '452.00', 2, 39, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(68, '2019-08-01', '4554.00', 7, 25, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(69, '2019-09-01', '5235.00', 4, 36, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(70, '2019-10-01', '4535.00', 9, 24, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(71, '2019-11-01', '6345.00', 2, 98, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(72, '2019-12-01', '4353.00', 9, 63, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(73, '2020-01-01', '321.00', 9, 62, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(74, '2020-02-01', '2121.00', 6, 145, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(75, '2020-03-01', '2321.00', 8, 32, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(76, '2020-04-01', '3213.00', 4, 19, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(77, '2020-05-01', '21312.00', 5, 20, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(78, '2020-06-01', '2321.00', 4, 14, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(79, '2020-07-01', '3213.00', 9, 20, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(80, '2020-08-01', '21312.00', 3, 30, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(81, '2020-09-01', '1234.00', 3, 31, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(82, '2020-10-01', '2124.00', 7, 50, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(83, '2020-11-01', '2432.00', 3, 95, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(84, '2020-12-01', '9845.00', 1, 120, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(85, '2021-01-01', '8342.00', 8, 90, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(86, '2021-02-01', '5451.00', 8, 85, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(87, '2021-03-01', '2352.00', 2, 70, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(88, '2021-04-01', '2355.00', 9, 50, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(89, '2021-05-01', '876.00', 3, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(90, '2021-06-01', '1245.00', 5, 40, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(91, '2021-07-01', '2355.00', 5, 31, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(92, '2021-08-01', '5325.00', 6, 32, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(93, '2021-09-01', '1523.80', 6, 46, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(94, '2021-10-01', '7895.00', 9, 53, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(95, '2021-11-01', '6574.00', 4, 80, '2022-09-20 04:00:00', '2022-09-20 04:00:00'),
(96, '2021-12-01', '8976.00', 5, 150, '2022-09-20 04:00:00', '2022-09-20 04:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2022_08_25_173051_create_categories_table', 1),
(5, '2022_08_27_030726_create_brands_table', 1),
(6, '2022_08_27_032232_create_providers_table', 1),
(7, '2022_08_27_041722_create_products_table', 1),
(8, '2022_08_29_025705_create_clients_table', 1),
(9, '2022_08_30_044055_create_purchases_table', 1),
(10, '2022_08_30_044402_create_purchase_details_table', 1),
(11, '2022_09_01_054842_create_sales_table', 1),
(12, '2022_09_01_055334_create_sale_details_table', 1),
(13, '2022_09_20_051249_create_historydatas_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sell_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('ACTIVO','DESACTIVADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `stock`, `image`, `sell_price`, `status`, `category_id`, `brand_id`, `provider_id`, `created_at`, `updated_at`) VALUES
(1, '1', 'Camara Fotografica Phenyx-z', 18, '1663704633_product-img7.jpg', '2500.00', 'ACTIVO', 1, 2, 2, '2022-09-21 00:10:33', '2022-10-10 18:27:19'),
(2, '2', 'Lentes de Camara T-STE7', 58, '1663704675_product-img8.jpg', '250.00', 'ACTIVO', 1, 1, 3, '2022-09-21 00:11:15', '2022-10-10 18:27:21'),
(3, '3', 'Parlantes Stereo STRDH190', 5, '1663704840_parlante sony.jpg', '1500.00', 'ACTIVO', 3, 1, 3, '2022-09-21 00:14:00', '2022-10-10 18:27:23'),
(4, '4', 'Monitor LED AS-213 22\"', 90, '1663705019_Philips-279C9.jpg', '900.00', 'ACTIVO', 8, 10, 1, '2022-09-21 00:16:59', '2022-09-21 00:16:59'),
(5, '5', 'Nintendo Switch - OLED', 0, '1663705439_nintendo-consola-switch.jpg', '3000.00', 'ACTIVO', 5, 6, 7, '2022-09-21 00:19:31', '2022-09-21 00:23:59'),
(6, '6', 'TV 40\" PLDED4016A', 0, '1663705210_samsung-ue32es5800.jpg', '4000.00', 'ACTIVO', 4, 10, 9, '2022-09-21 00:20:10', '2022-09-21 00:20:10'),
(7, '7', 'PlayStation 5', 0, '1663705244_depositphotos_382557402-stock-photo-japan-june-11-2020-presentation.jpg', '5000.00', 'ACTIVO', 5, 1, 2, '2022-09-21 00:20:44', '2022-09-21 00:20:44'),
(8, '8', 'Tarjeta de memoria Micro SD 128 GB', 0, '1663705276_product-img13.jpg', '15000.00', 'ACTIVO', 6, 9, 1, '2022-09-21 00:21:16', '2022-09-21 00:21:16'),
(9, '9', 'Proyector 1080P - Native', 0, '1663705460_f.elconfidencial.com_original_633_db3_caa_633db3caa4aafa2ea1ce4f3a7a008b73.jpg', '2500.00', 'ACTIVO', 7, 1, 1, '2022-09-21 00:21:57', '2022-09-21 00:24:20'),
(10, '10', 'Aspire 5 A515-56-32DK', 0, '1663705371_hp-envy-15-2020-08.webp', '4500.00', 'ACTIVO', 9, 9, 1, '2022-09-21 00:22:51', '2022-09-21 00:22:51'),
(11, '11', 'Punto de acceso móvil Nighthawk M5', 0, '1663705600_router-wifi-dual-doble-banda.jpg', '560.00', 'ACTIVO', 10, 9, 1, '2022-09-21 00:25:52', '2022-09-21 00:26:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `providers`
--

INSERT INTO `providers` (`id`, `name`, `email`, `nit`, `address`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Grupo DISAR', 'disar123@gmail.com', '12436587091', 'El alto av siempre viva 123', '67812123', '2022-09-21 00:00:58', '2022-09-21 00:00:58'),
(2, 'Apokin Mayorista Accesorios', 'apokin123@gmail.com', '98760967321', 'Alto Lima 1ra sección, calle Tiquina #6360', '65871243', '2022-09-21 00:02:14', '2022-09-21 00:02:14'),
(3, 'BELTRONICA', 'belt_123@gmail.com', '76983412341', 'Alto Tiquina #6360', '78612432', '2022-09-21 00:03:11', '2022-09-21 00:03:11'),
(4, 'Dómux', 'domux123@gmail.com', '87896754321', 'Alto Lima 1ra sección, calle Tiquina #6360', '65871242', '2022-09-21 00:03:59', '2022-09-21 00:03:59'),
(5, 'Tektronic 2000', 'telt_123@gmail.com', '09875632115', 'El alto av siempre viva 123', '78982134', '2022-09-21 00:04:45', '2022-09-21 00:04:45'),
(6, 'Dishom', 'dish324@gmail.com', '89675432131', 'Alto Lima 1ra sección, calle Tiquina #6360', '67872143', '2022-09-21 00:05:21', '2022-09-21 00:05:21'),
(7, 'INTEREC', 'interec123@gmail.com', '65870934561', 'Alto Lima 1ra sección, calle Tiquina #6360', '65870912', '2022-09-21 00:05:48', '2022-09-21 00:05:48'),
(8, 'Electro Devasa', 'elect@gmail.com', '09876754451', 'Alto Lima 1ra sección, calle Tiquina #6360', '78096534', '2022-09-21 00:06:18', '2022-09-21 00:06:18'),
(9, 'Media MicroComputer', 'media123@gmail.com', '67893265891', 'Alto Lima 1ra sección, calle Tiquina #6360', '65982312', '2022-09-21 00:06:51', '2022-09-21 00:06:51'),
(10, 'Vofeel Tech', 'vofeelech123@gmail.com', '78095634214', 'Alto Lima 1ra sección, calle Tiquina #6360', '67943254', '2022-09-21 00:07:26', '2022-09-21 00:07:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` datetime NOT NULL,
  `tax` decimal(8,2) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `status` enum('CONFIRMADO','CANCELADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CONFIRMADO',
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchases`
--

INSERT INTO `purchases` (`id`, `provider_id`, `user_id`, `purchase_date`, `tax`, `total`, `status`, `picture`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2022-09-20 16:42:56', '0.00', '50000.00', 'CANCELADO', NULL, '2022-09-21 00:42:56', '2022-10-11 05:01:29'),
(2, 1, 1, '2022-09-20 16:43:55', '10.00', '3960.00', 'CANCELADO', NULL, '2022-09-21 00:43:55', '2022-09-21 00:49:10'),
(3, 3, 1, '2022-09-20 16:48:16', '10.00', '8250.00', 'CONFIRMADO', NULL, '2022-09-21 00:48:16', '2022-09-21 00:48:16'),
(4, 4, 1, '2022-09-20 17:40:49', '10.00', '1496.00', 'CONFIRMADO', NULL, '2022-09-21 01:40:49', '2022-09-21 01:40:49'),
(5, 3, 1, '2022-10-05 23:48:17', '10.00', '1152.80', 'CANCELADO', NULL, '2022-10-06 07:48:17', '2022-10-07 01:16:46'),
(6, 3, 1, '2022-10-06 20:13:40', '10.00', '1100.00', 'CONFIRMADO', NULL, '2022-10-07 04:13:40', '2022-10-07 04:13:40'),
(7, 1, 1, '2022-10-06 20:14:31', '10.00', '73975.00', 'CONFIRMADO', NULL, '2022-10-07 04:14:31', '2022-10-07 04:14:31');

--
-- Disparadores `purchases`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockCompraAnular` AFTER UPDATE ON `purchases` FOR EACH ROW BEGIN
  UPDATE products p
    JOIN purchase_details di
      ON di.product_id = p.id
     AND di.purchase_id = new.id
     set p.stock = p.stock - di.quantity;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_details`
--

INSERT INTO `purchase_details` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 10, '750.00', '2022-09-21 00:48:16', '2022-09-21 00:48:16'),
(2, 4, 2, 4, '340.00', '2022-09-21 01:40:49', '2022-09-21 01:40:49'),
(3, 5, 4, 4, '250.00', '2022-10-06 07:48:17', '2022-10-06 07:48:17'),
(4, 5, 7, 4, '12.00', '2022-10-06 07:48:17', '2022-10-06 07:48:17'),
(5, 6, 1, 10, '100.00', '2022-10-07 04:13:40', '2022-10-07 04:13:40'),
(6, 7, 4, 10, '100.00', '2022-10-07 04:14:31', '2022-10-07 04:14:31'),
(7, 7, 2, 15, '250.00', '2022-10-07 04:14:31', '2022-10-07 04:14:31'),
(8, 7, 3, 5, '500.00', '2022-10-07 04:14:31', '2022-10-07 04:14:31'),
(9, 7, 4, 60, '1000.00', '2022-10-07 04:14:31', '2022-10-07 04:14:31');

--
-- Disparadores `purchase_details`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockCompra` AFTER INSERT ON `purchase_details` FOR EACH ROW BEGIN
 UPDATE products SET stock = stock + NEW.quantity
 WHERE products.id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sale_date` datetime NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `status` enum('CONFIRMADO','CANCELADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CONFIRMADO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `client_id`, `user_id`, `sale_date`, `total`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2022-09-20 16:51:03', '9000.00', 'CONFIRMADO', '2022-09-21 00:51:03', '2022-09-21 00:51:03'),
(2, 2, 1, '2022-09-20 17:34:12', '9000.00', 'CANCELADO', '2022-09-21 01:34:12', '2022-09-21 01:34:35'),
(3, 3, 1, '2022-10-05 11:00:22', '10400.00', 'CONFIRMADO', '2022-10-05 19:00:22', '2022-10-05 19:00:22'),
(4, 2, 1, '2022-10-06 00:18:06', '725.00', 'CONFIRMADO', '2022-10-06 08:18:06', '2022-10-06 08:18:06'),
(5, 1, 1, '2022-10-06 16:46:54', '2520.00', 'CONFIRMADO', '2022-10-07 00:46:54', '2022-10-07 00:46:54'),
(6, 2, 1, '2022-10-06 17:45:13', '4500.00', 'CONFIRMADO', '2022-10-07 01:45:13', '2022-10-07 01:45:13'),
(7, 2, 1, '2022-10-06 19:52:44', '450.00', 'CONFIRMADO', '2022-10-07 03:52:44', '2022-10-07 03:52:44'),
(8, 1, 1, '2022-10-06 20:18:13', '5625.00', 'CONFIRMADO', '2022-10-07 04:18:13', '2022-10-07 04:18:13');

--
-- Disparadores `sales`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockVentaAnular` AFTER UPDATE ON `sales` FOR EACH ROW BEGIN
  UPDATE products p
    JOIN sale_details dv
      ON dv.product_id = p.id
     AND dv.sale_id= new.id
     set p.stock = p.stock + dv.quantity;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `discount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, '2500.00', '10.00', '2022-09-21 00:51:03', '2022-09-21 00:51:03'),
(2, 2, 1, 4, '2500.00', '10.00', '2022-09-21 01:34:12', '2022-09-21 01:34:12'),
(3, 3, 1, 4, '2500.00', '0.00', '2022-10-05 19:00:22', '2022-10-05 19:00:22'),
(4, 3, 2, 2, '250.00', '20.00', '2022-10-05 19:00:22', '2022-10-05 19:00:22'),
(5, 4, 2, 1, '250.00', '10.00', '2022-10-06 08:18:06', '2022-10-06 08:18:06'),
(6, 4, 2, 2, '250.00', '0.00', '2022-10-06 08:18:06', '2022-10-06 08:18:06'),
(7, 5, 4, 2, '900.00', '10.00', '2022-10-07 00:46:54', '2022-10-07 00:46:54'),
(8, 5, 4, 1, '900.00', '0.00', '2022-10-07 00:46:54', '2022-10-07 00:46:54'),
(9, 6, 1, 2, '2500.00', '10.00', '2022-10-07 01:45:13', '2022-10-07 01:45:13'),
(10, 7, 2, 2, '250.00', '10.00', '2022-10-07 03:52:44', '2022-10-07 03:52:44'),
(11, 8, 1, 2, '2500.00', '10.00', '2022-10-07 04:18:13', '2022-10-07 04:18:13'),
(12, 8, 2, 5, '250.00', '10.00', '2022-10-07 04:18:13', '2022-10-07 04:18:13');

--
-- Disparadores `sale_details`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockVenta` AFTER INSERT ON `sale_details` FOR EACH ROW BEGIN
 UPDATE products SET stock = stock - NEW.quantity
 WHERE products.id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador 1', 'danilunajose123@gmail.com', NULL, '$2y$10$q4ArRpspUgRvMGIQlS7AreESvOgvzERDPPB9X5N98VeI9SeCTLzXq', NULL, '2022-09-20 23:40:57', '2022-09-20 23:40:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_ci_unique` (`ci`),
  ADD UNIQUE KEY `clients_phone_unique` (`phone`),
  ADD UNIQUE KEY `clients_nit_unique` (`nit`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historydatas`
--
ALTER TABLE `historydatas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historydatas_category_id_foreign` (`category_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_name_unique` (`name`),
  ADD UNIQUE KEY `products_code_unique` (`code`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_provider_id_foreign` (`provider_id`);

--
-- Indices de la tabla `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `providers_name_unique` (`name`),
  ADD UNIQUE KEY `providers_email_unique` (`email`),
  ADD UNIQUE KEY `providers_nit_unique` (`nit`),
  ADD UNIQUE KEY `providers_phone_unique` (`phone`);

--
-- Indices de la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_provider_id_foreign` (`provider_id`),
  ADD KEY `purchases_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_details_product_id_foreign` (`product_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_client_id_foreign` (`client_id`),
  ADD KEY `sales_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_details_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_details_product_id_foreign` (`product_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historydatas`
--
ALTER TABLE `historydatas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historydatas`
--
ALTER TABLE `historydatas`
  ADD CONSTRAINT `historydatas_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`);

--
-- Filtros para la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`),
  ADD CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`);

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
