<?php

require '../api/crearToken.php';
include '../bbdd/conexion.php';
require '../verificarToken.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


$headers = apache_request_headers();
if (!isset($headers['Authorization']) || !preg_match('/Bearer\s(\S+)/',
        $headers['Authorization'], $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
    exit;
}

$token = $matches[1];
$user_id = verificarToken($token);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
    exit;
}

if (empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing 'id' parameter."]);
    exit;
}

$credentials = obtenerToken();

$user_id = $_GET['id'];

if (isset($credentials['error'])) {
    echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
    exit;
}


$sql = "SELECT * FROM twitchusers WHERE idUser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    echo json_encode(json_decode($user_data["data"]), JSON_PRETTY_PRINT);
    $conn->close();
    exit;
} else {
    $client_id = $credentials['client_id'];
    $access_token = $credentials['access_token'];

    $api_url = "https://api.twitch.tv/helix/users?id=$user_id";

    $headers = [
        "Client-ID: $client_id",
        "Authorization: Bearer $access_token"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $data = json_decode($response, true);
        if (!isset($data["data"][0])) {
            http_response_code(404);
            echo json_encode(["error" => "User not found."]);
            exit;
        }

        $streamer = $data["data"][0];
        $resultado = [
            "id" => $streamer["id"],
            "login" => $streamer["login"],
            "display_name" => $streamer["display_name"],
            "type" => $streamer["type"],
            "broadcaster_type" => $streamer["broadcaster_type"],
            "description" => $streamer["description"],
            "profile_image_url" => $streamer["profile_image_url"],
            "offline_image_url" => $streamer["offline_image_url"],
            "view_count" => $streamer["view_count"],
            "created_at" => $streamer["created_at"]
        ];

        $data_json = json_encode($resultado);
        $insert_sql = "INSERT INTO twitchusers (idUser, data) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ss", $streamer["id"], $data_json);
        $insert_stmt->execute();

        echo json_encode($resultado, JSON_PRETTY_PRINT);
    } elseif ($http_code == 400) {
        http_response_code($http_code);
        echo json_encode(["error" => "RESPONSE 400: Invalid or missing 'id' parameter."]);
    } elseif ($http_code == 401) {
        http_response_code($http_code);
        echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
    } elseif ($http_code == 404) {
        http_response_code($http_code);
        echo json_encode(["error" => "RESPONSE 404: User not found."]);
    } elseif ($http_code == 500) {
        http_response_code($http_code);
        echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
    } else {
        echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
    }
    $conn->close();
}
