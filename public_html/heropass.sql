-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: db5017192845.hosting-data.io
-- Tiempo de generación: 18-02-2025 a las 12:42:34
-- Versión del servidor: 8.0.36
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbs13808414`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `id` int NOT NULL,
  `endpoint` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data` text COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`id`, `endpoint`, `data`, `timestamp`) VALUES
(13, 'topsofthetops', '[\n    {\n        \"game_id\": \"509658\",\n        \"game_name\": \"Just Chatting\",\n        \"user_name\": \"KaiCenat\",\n        \"total_videos\": 36,\n        \"total_views\": 414852488,\n        \"most_viewed_title\": \"\\ud83e\\udd83 MAFIATHON 2 \\ud83e\\udd83 KAI X KEVIN HART X DRUSKI \\ud83e\\udd83 DAY 27 \\ud83e\\udd83 20% OF REVENUE GOING TO SCHOOL IN NIGERIA \\ud83e\\udd83 ALL MONTH \\ud83e\\udd83 CLICK HERE \\ud83e\\udd83 !Subathon\",\n        \"most_viewed_views\": 24867713,\n        \"most_viewed_duration\": \"22h5m32s\",\n        \"most_viewed_created_at\": \"2024-11-28T02:06:07Z\"\n    },\n    {\n        \"game_id\": \"21779\",\n        \"game_name\": \"League of Legends\",\n        \"user_name\": \"Riot Games\",\n        \"total_videos\": 26,\n        \"total_views\": 124973786,\n        \"most_viewed_title\": \"WORLDS 22 FINALS COUNTDOWN\",\n        \"most_viewed_views\": 11620692,\n        \"most_viewed_duration\": \"9h25m12s\",\n        \"most_viewed_created_at\": \"2022-11-05T21:00:23Z\"\n    },\n    {\n        \"game_id\": \"32399\",\n        \"game_name\": \"Counter-Strike\",\n        \"user_name\": \"ESLCS\",\n        \"total_videos\": 26,\n        \"total_views\": 108484321,\n        \"most_viewed_title\": \"LIVE: Team Spirit vs Heroic - IEM Rio 2022 - Champions Stage Quaterfinal\",\n        \"most_viewed_views\": 5925091,\n        \"most_viewed_duration\": \"9h30m2s\",\n        \"most_viewed_created_at\": \"2022-11-11T15:40:22Z\"\n    }\n]', '2025-02-18 11:23:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `token`, `expires_at`) VALUES
(3, 2, 'ab7ecdeaa06336505d1781576c805f47', '2025-02-16 16:20:49'),
(6, 4, '2b10adb9eac409e5c86c42216ba276f6', '2025-02-16 16:59:37'),
(7, 5, 'c1c66054c9733bbe4c06fe90c40c7556', '2025-02-16 17:01:50'),
(8, 4, 'edc858d84b576c8a2f31e99744eb1f65', '2025-02-16 17:05:54'),
(9, 8, '6f500e64e47b6f6dd57ecc666346370a', '2025-02-20 19:54:52'),
(10, 4, '39993f7cd166afd656c32e87593feb30', '2025-02-21 09:23:47'),
(11, 4, '293a862a36197181152456a6dc42809d', '2025-02-21 09:30:50'),
(12, 4, '2afe76d941d33350da9fb805272b6088', '2025-02-21 11:05:25'),
(13, 9, '59071c4d9b296b2f0c3e75a30677d956', '2025-02-21 11:51:51'),
(14, 9, 'cc50a856561e415e6434781462922528', '2025-02-21 11:52:12'),
(15, 9, 'ebd39a62f897972182b9dbb8cfb18703', '2025-02-21 11:52:26'),
(16, 9, '2468e87e9a3b50527caf4edd52040da9', '2025-02-21 11:52:32'),
(17, 9, '6a4eb4f051ef8b1ff18a7baba2581d45', '2025-02-21 11:52:33'),
(18, 9, 'f9ecf025270f021b0a250c1da42480c6', '2025-02-21 11:52:41'),
(19, 4, 'eb82701f5689ce2ca13e921be109b267', '2025-02-21 11:57:51'),
(20, 10, 'ab2da02f305067e0c27a1f47c65308f6', '2025-02-21 11:59:26'),
(21, 4, 'f2ec411b2e61177341e3190bfb4e93a6', '2025-02-21 12:01:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `api_key`) VALUES
(1, 'tt@gmail.com', '77a6b3b0235c8eaa'),
(2, 'usuario@ejemplo.com', '5fad8d04c81b4c9109a468c01710838b'),
(4, 'usuario@example.com', '134eb0c7fb01287ad682b3c19b341805'),
(5, 'prueba@ejemplo.com', 'c09d7f0158595c67925fe3c06559ec0c'),
(6, 'hola@hola.com', '7d5e37037119c8f67b16df65f8cda8f9'),
(7, 'aznarez.138165@gmail.com', 'a418ea3054bc2d95c0d24da8870e9227'),
(8, 'heropin@gmail.com', '575549d202d5c38569b255b6ab36e656'),
(9, 'prueba1@prueba1.com', 'fa1c94f969a98d3bf2bd1113963f6bdc'),
(10, 'prueba2@prueba2.com', '0ce0ec097bbbbb5b97b9e4e8ccfcba4a');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cache`
--
ALTER TABLE `cache`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
