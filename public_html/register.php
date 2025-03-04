<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "The email must be a valid email address"]);
    exit;
}

$email = $data['email'];
$api_key = bin2hex(random_bytes(16));

// Conectarse a bbdd
require_once 'bbdd/conexion.php';



try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) { //rowCount() devuelve el número de filas afectadas por la última sentencia SQL
        // El email ya existe, actualizar api_key
        $stmt = $pdo->prepare("UPDATE users SET api_key = :api_key WHERE email = :email");
    } else {
        // El email no existe, insertar nuevo registro
        $stmt = $pdo->prepare("INSERT INTO users (email, api_key) VALUES (:email, :api_key)");
    }

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':api_key', $api_key);
    $stmt->execute();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}

echo json_encode(["api_key" => $api_key]);
// cerrar la conexión
$conn->close();
