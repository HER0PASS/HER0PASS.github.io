<?php

// Obtener valores de las variables de entorno si están disponibles
$host = getenv('DB_HOST') ?: "mysql";
$user = getenv('DB_USERNAME') ?: "root";
$password = getenv('DB_PASSWORD') ?: "root";
$database = getenv('DB_DATABASE') ?: "lumen";

try {
    // Crear conexión
    $conn = new mysqli($host, $user, $password, $database);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("❌ Conexión fallida: " . $conn->connect_error);
        // En lugar de morir, configurar $conn como null para manejar el error más adelante
        $conn = null;
    }
} catch (Exception $e) {
    error_log("Error de conexión: " . $e->getMessage());
    // Configurar $conn como null para manejar el error
    $conn = null;
}
