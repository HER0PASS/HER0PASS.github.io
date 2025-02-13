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

if (!isset($data['api_key'])) {
    http_response_code(400);
    echo json_encode(["error" => "The api_key is mandatory"]);
    exit;
}

$email = $data['email'];
$api_key = $data['api_key'];

// Incluir el archivo de conexión
require_once 'bbdd/conexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Comprobar si el email y la API Key existen
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND api_key = :api_key");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':api_key', $api_key);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid email or api_key"]);
        exit;
    }

    // Generar un token de sesión
    $token = bin2hex(random_bytes(16));
    $expiration = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60)); // 3 días

    // Guardar el token en la base de datos
    $save_stmt = $pdo->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
    $save_stmt->bindParam(':user_id', $user['id']);
    $save_stmt->bindParam(':token', $token);
    $save_stmt->bindParam(':expires_at', $expiration);
    $save_stmt->execute();

    echo json_encode(["token" => $token]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
