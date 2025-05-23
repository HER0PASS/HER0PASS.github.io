<?php

// Obtener valores de las variables de entorno si están disponibles, sino hardcodearlas (esto hay que quitarlo)
$host     = getenv('DB_HOST') ?: "mysql";
$user     = getenv('DB_USERNAME') ?: "lumen";
$password = getenv('DB_PASSWORD') ?: "lumen";
$database = getenv('DB_DATABASE') ?: "lumen";

/*
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    error_log("Conexión fallida: " . $conn->connect_error);
    throw new Exception("MySQLi connection failed: {$conn->connect_error}");
}
*/

$pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
