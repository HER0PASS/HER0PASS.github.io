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

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                         `top` int DEFAULT NULL,
                         `data` text,
                         PRIMARY KEY (`id`)
);

INSERT INTO `cache` (`top`, `data`, `timestamp`) VALUES
                                                     (1, '{
  "game_id": "509658",
  "game_name": "Just Chatting",
  "user_name": "KaiCenat",
  "total_videos": 36,
  "total_views": 414852488,
  "most_viewed_title": "MAFIATHON 2 KAI X KEVIN HART X DRUSKI",
  "most_viewed_views": 24867713,
  "most_viewed_duration": "22h5m32s",
  "most_viewed_created_at": "2024-11-28T02:06:07Z"
}', '2025-02-18 11:23:50'),
                                                     (2, '{
  "game_id": "21779",
  "game_name": "League of Legends",
  "user_name": "Riot Games",
  "total_videos": 26,
  "total_views": 124973786,
  "most_viewed_title": "WORLDS 22 FINALS COUNTDOWN",
  "most_viewed_views": 11620692,
  "most_viewed_duration": "9h25m12s",
  "most_viewed_created_at": "2022-11-05T21:00:23Z"
}', '2025-02-18 11:23:50'),
                                                     (3, '{
  "game_id": "32399",
  "game_name": "Counter-Strike",
  "user_name": "ESLCS",
  "total_videos": 26,
  "total_views": 108484321,
  "most_viewed_title": "LIVE: Team Spirit vs Heroic - IEM Rio 2022",
  "most_viewed_views": 5925091,
  "most_viewed_duration": "9h30m2s",
  "most_viewed_created_at": "2022-11-11T15:40:22Z"
}', '2025-02-18 11:23:50');
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
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
(6, 4, '41d2562ddc215251d5c6dfd86c44d16b', '2025-04-26 17:12:02'),
(7, 5, 'c1c66054c9733bbe4c06fe90c40c7556', '2025-02-16 17:01:50'),
(9, 8, '6f500e64e47b6f6dd57ecc666346370a', '2025-02-20 19:54:52'),
(20, 10, 'ab2da02f305067e0c27a1f47c65308f6', '2025-02-21 11:59:26'),
(24, 9, 'b19cf0510af0d0e2d2f541f01313b981', '2025-04-26 17:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TwitchUsers`
--

CREATE TABLE IF NOT EXISTS `TwitchUsers` (
  `idUser` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `TwitchUsers`
--

INSERT INTO `TwitchUsers` (`idUser`, `data`) VALUES
('509658', '{"id": "509658", "login": "KaiCenat", "display_name": "KaiCenat", "broadcaster_type": "partner", "description": "Si lees esto que sepas que te aprecio", "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/574228be-01ef-4eab-bc0e-a4f6b68bedba-profile_image-300x300.png", "view_count": 0, "created_at": "2015-02-20T16:47:56Z"}'),
('21779', '{"id": "21779", "login": "Riot Games", "display_name": "Riot Games", "broadcaster_type": "partner", "description": "Si lees esto que sepas que te aprecio", "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/574228be-01ef-4eab-bc0e-a4f6b68bedba-profile_image-300x300.png", "view_count": 0, "created_at": "2015-02-20T16:47:56Z"}'),
('32399', '{"id": "32399", "login": "ESLCS", "display_name": "ESLCS", "broadcaster_type": "partner", "description": "Si lees esto que sepas que te aprecio", "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/574228be-01ef-4eab-bc0e-a4f6b68bedba-profile_image-300x300.png", "view_count": 0, "created_at": "2015-02-20T16:47:56Z"}');
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
