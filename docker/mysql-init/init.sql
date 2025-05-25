
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `lumen`
--

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
                                       `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                       `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `api_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
    );

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
                                          `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
                                          `user_id` int UNSIGNED NOT NULL,
                                          `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `expires_at` timestamp NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    );

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

--
-- Estructura de tabla para la tabla `TwitchUsers`
--

CREATE TABLE IF NOT EXISTS `TwitchUsers` (
                                             `idUser` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `data` text COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`idUser`)
    );

-- Commit de la transacción
COMMIT;
