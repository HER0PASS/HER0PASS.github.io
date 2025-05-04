<?php

$host = "mysql";         // nombre del servicio en docker-compose
$user = "lumen";         // definido en docker-compose.yml
$password = "lumen";     // igual
$database = "lumen";     // nombre de la base de datos creada en init.sql

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}
