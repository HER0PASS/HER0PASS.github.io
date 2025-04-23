<?php

function verificarToken($token)
{
    require 'bbdd/conexion.php';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE token = :token AND expires_at > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($session) {
            return $session['user_id'];
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}

function gestionarTokenUsuario($user_id)
{
    require 'bbdd/conexion.php';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $session_stmt = $pdo->prepare("SELECT token, expires_at FROM sessions WHERE user_id = :user_id");
        $session_stmt->bindParam(':user_id', $user_id);
        $session_stmt->execute();
        $session = $session_stmt->fetch(PDO::FETCH_ASSOC);

        $now = date('Y-m-d H:i:s');
        if ($session) {
            if ($session['expires_at'] > $now) {
                return $session['token'];
            } else {
                $token = bin2hex(random_bytes(16));
                $expiration = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));

                $update_stmt = $pdo->prepare("UPDATE sessions SET token = :token, expires_at = :expires_at WHERE user_id = :user_id");
                $update_stmt->bindParam(':token', $token);
                $update_stmt->bindParam(':expires_at', $expiration);
                $update_stmt->bindParam(':user_id', $user_id);
                $update_stmt->execute();

                return $token;
            }
        } else {
            $token = bin2hex(random_bytes(16));
            $expiration = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));

            $insert_stmt = $pdo->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
            $insert_stmt->bindParam(':user_id', $user_id);
            $insert_stmt->bindParam(':token', $token);
            $insert_stmt->bindParam(':expires_at', $expiration);
            $insert_stmt->execute();

            return $token;
        }
    } catch (PDOException $e) {
        return false;
    }
}

function handleRequest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email']) || empty($data['email'])) {
        http_response_code(400);
        echo json_encode(["error" => "The email is mandatory"]);
        exit;
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "The email must be a valid email address"]);
        exit;
    }

    if (!isset($data['api_key'])) {
        http_response_code(400);
        echo json_encode(["error" => "The api_key is mandatory"]);
        exit;
    }

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    $email = $data['email'];
    $api_key = $data['api_key'];
    require 'bbdd/conexion.php';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = :email AND api_key = :api_key"
        );
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized. API access token is invalid."]);
            exit;
        }

        $token = gestionarTokenUsuario($user['id']);
        if ($token) {
            echo json_encode(["token" => $token]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Internal server error"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Internal server error"]);
        exit;
    }

    $pdo = null;
}
