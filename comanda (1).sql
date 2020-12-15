-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-12-2020 a las 05:00:33
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `description` text COLLATE utf8_spanish2_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `responsable` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `items`
--

INSERT INTO `items` (`id`, `description`, `price`, `responsable`, `creation_date`, `last_update`) VALUES
(1, 'Cerveza Rubia', '100.00', 'cervecero', '2020-12-11 01:01:44', '2020-12-12 18:30:52'),
(2, 'Cerveza Roja', '120.00', 'cervecero', '2020-12-11 01:04:52', '2020-12-12 18:30:10'),
(3, 'Empanada de Carne', '80.75', 'cocinero', '2020-12-12 14:35:39', '2020-12-12 18:36:33'),
(4, 'Empanada de Pollo', '80.75', 'cocinero', '2020-12-12 19:13:02', '2020-12-12 19:13:02'),
(5, 'Hamburguesa', '180.00', 'cocinero', '2020-12-12 19:13:14', '2020-12-12 19:13:14'),
(6, 'Papas Fritas', '100.00', 'cocinero', '2020-12-12 19:13:29', '2020-12-12 19:13:29'),
(7, 'Vino Malbec', '200.00', 'bartender', '2020-12-12 19:14:01', '2020-12-12 19:14:01'),
(8, 'Vino Pinot', '220.00', 'bartender', '2020-12-12 19:14:15', '2020-12-12 19:14:15'),
(9, 'Cuba Libre', '110.00', 'bartender', '2020-12-12 19:14:24', '2020-12-12 19:14:24'),
(10, 'Gin Tonic', '125.00', 'bartender', '2020-12-12 19:14:35', '2020-12-12 19:14:35'),
(11, 'Cerveza Negra', '125.00', 'cervecero', '2020-12-12 19:15:19', '2020-12-12 19:15:19'),
(12, 'Cerveza IPA', '125.00', 'cervecero', '2020-12-12 19:15:25', '2020-12-12 19:18:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `guid` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `id_table` int(11) NOT NULL,
  `status` varchar(30) COLLATE utf8_spanish2_ci NOT NULL,
  `creation_date` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `last_update` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `guid`, `id_table`, `status`, `creation_date`, `last_update`) VALUES
(5, '643d2', 5, 'Servido', '2020-12-12 19:25:31.462774', '2020-12-13 14:55:51.000000'),
(6, '3e541', 3, 'En preparación', '2020-12-13 14:48:52.305502', '2020-12-13 14:55:57.000000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_lines`
--

CREATE TABLE `order_lines` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `expected_time` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `order_lines`
--

INSERT INTO `order_lines` (`id`, `order_id`, `item_id`, `quantity`, `status`, `expected_time`, `creation_date`, `last_update`) VALUES
(7, 5, 3, 1, 'Recepcionado', 0, '2020-12-12 19:25:31', '2020-12-12 19:25:31'),
(8, 5, 4, 1, 'Recepcionado', 0, '2020-12-12 19:25:31', '2020-12-12 19:25:31'),
(9, 5, 7, 1, 'Servido', 0, '2020-12-12 19:25:31', '2020-12-13 03:20:51'),
(10, 5, 5, 2, 'Recepcionado', 0, '2020-12-12 19:25:31', '2020-12-12 19:25:31'),
(11, 5, 6, 1, 'Listo para servir', 10, '2020-12-12 19:25:31', '2020-12-13 03:10:55'),
(12, 6, 3, 2, 'En preparación', 20, '2020-12-13 14:48:52', '2020-12-13 15:29:36'),
(13, 6, 1, 2, 'Servido', 40, '2020-12-13 14:48:52', '2020-12-13 15:44:54'),
(14, 6, 6, 1, 'En preparación', 20, '2020-12-13 14:48:52', '2020-12-13 15:29:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `id_table` varchar(11) COLLATE utf8_spanish2_ci NOT NULL,
  `table_value` tinyint(4) NOT NULL,
  `restaurant_value` tinyint(4) NOT NULL,
  `chef_value` tinyint(4) NOT NULL,
  `waiter_value` int(11) NOT NULL,
  `comment` varchar(66) COLLATE utf8_spanish2_ci NOT NULL,
  `creation_date` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `last_update` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `polls`
--

INSERT INTO `polls` (`id`, `id_table`, `table_value`, `restaurant_value`, `chef_value`, `waiter_value`, `comment`, `creation_date`, `last_update`) VALUES
(5, 'A0004', 6, 7, 6, 5, 'hola', '2020-12-13 22:05:13.557653', '2020-12-13 22:05:13.557653');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `table_id` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `status` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tables`
--

INSERT INTO `tables` (`id`, `table_id`, `status`, `creation_date`, `last_update`) VALUES
(1, 'A0001', 'Cerrada', '2020-12-06 18:56:40', '2020-12-06 18:56:40'),
(2, 'A0002', 'Cerrada', '2020-12-06 18:56:54', '2020-12-06 23:26:56'),
(3, 'A0003', 'Con clientes esperando pedido', '2020-12-06 18:56:59', '2020-12-13 14:49:02'),
(4, 'A0004', 'Con clientes pagando', '2020-12-06 18:57:01', '2020-12-13 21:52:28'),
(5, 'A0005', 'Con clientes esperando pedido', '2020-12-06 18:57:05', '2020-12-12 23:46:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `area` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `area`, `created_at`, `updated_at`) VALUES
(1, 'marcos', '355cb48fb7451157d6b6998ae30f0edd3e5b89b2ec4d84c55958237775a2bb0e86e8f0bb747ad579aa2d076fb7c46c9027fd9963e7718a46682a234dcb619864', 'admin', '2020-11-11 04:35:12', '2020-11-11 08:27:03'),
(3, 'flopa', '9cba574a2da0bd3f4ce8032db5f714826ec3cf512fe57db5cf8baf7d880aac72f1235283858b07fc6f76850ccc2d739439d927003c050e3d1e1da205c75c5dc5', 'admin', '2020-11-13 05:27:21', '2020-11-13 05:27:21'),
(4, 'socio1', '$2y$10$KUjKS00.7SWKWNlw3eKxPuJofP93I/sCbzmZABnlm71gvbGCDRdna', 'socio', '2020-12-06 20:38:55', '2020-12-06 20:38:55'),
(5, 'carlos', '$2y$10$YiHlQplLWScK6UKjm8OLTuMkEgAasrsgsYTAOMZFo/lIWRqzutfhu', 'admin', '2020-12-06 20:41:15', '2020-12-06 20:41:15'),
(6, 'pepe', '$2y$10$iI4GIX9hi18EVPV9u.v/deJXhF5jJAoCOGfm1.IXEI63K6hCuEOUW', 'mozo', '2020-12-13 01:01:22', '2020-12-13 01:01:22'),
(7, 'juan', '$2y$10$OMmLthFM6j3VErSJ46gyEeOSLXo.PKzEBx.KJuvVJVLb0JkAMgUkK', 'bartender', '2020-12-13 02:21:48', '2020-12-13 02:21:48'),
(8, 'tito', '$2y$10$2uabJtLqs8vNM1J67w/t2.3D0pYfSDCFZemrmt9NdkXZFKMnZetuu', 'cocinero', '2020-12-13 02:27:24', '2020-12-13 02:27:24'),
(9, 'pipo', '$2y$10$y2UEy3o2ZVsF8R0Prw9md.goHsRDeM1WhPFoH6cz1IX12Q0gZlP/a', 'cervecero', '2020-12-13 03:22:00', '2020-12-13 03:22:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mesas` (`id_table`);

--
-- Indices de la tabla `order_lines`
--
ALTER TABLE `order_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item` (`item_id`),
  ADD KEY `order` (`order_id`);

--
-- Indices de la tabla `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table` (`id_table`);

--
-- Indices de la tabla `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `order_lines`
--
ALTER TABLE `order_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `order_lines`
--
ALTER TABLE `order_lines`
  ADD CONSTRAINT `item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
