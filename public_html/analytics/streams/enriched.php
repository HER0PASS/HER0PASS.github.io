<?php

require './../../api/crearToken.php';
include './../../bbdd/conexion.php';
require './../../verificarToken.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$headers = apache_request_headers();
if (!isset($headers['Authorization']) || !preg_match('/Bearer\s(\S+)/',
        $headers['Authorization'], $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Authorization header missing or invalid"]);
    exit;
}

$token = $matches[1];
$user_id = verificarToken($token);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

$credentials = obtenerToken();

if (isset($credentials['error'])) {
    http_response_code(401);
    echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
    exit;
}

if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || $_GET['limit'] <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing 'limit' parameter."]);
    exit;
}

$limit = $_GET['limit'];
$client_id = $credentials['client_id'];
$access_token = $credentials['access_token'];
$api_url = "https://api.twitch.tv/helix/streams?first=$limit";

$headers = [
    "Client-ID: $client_id",
    "Authorization: Bearer $access_token"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $data = json_decode($response, true);

    if (!isset($data["data"])) {
        echo json_encode(["error" => "No se encontraron streams"]);
        exit;
    }

    $streams_filtrados = array_map(function ($stream) {
        return [
            "stream_id" => $stream["id"],
            "user_id" => $stream["user_id"],
            "user_name" => $stream["user_login"],
            "viewer_count" => $stream["viewer_count"],
            "title" => $stream["title"]
        ];
    }, $data["data"]);

    $user_ids = array_column($streams_filtrados, 'user_id');
    $user_ids_str = implode('&id=', $user_ids);
    $user_api_url = "https://api.twitch.tv/helix/users?id=" . $user_ids_str;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $user_api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $user_response = curl_exec($ch);
    curl_close($ch);

    $user_data = json_decode($user_response, true);

    $user_info = [];
    foreach ($user_data['data'] as $user) {
        $user_info[$user['id']] = [
            'user_display_name' => $user['display_name'],
            'profile_image_url' => $user['profile_image_url']
        ];
    }

    $streams_enriquecidos = array_map(function ($stream) use ($user_info) {
        return array_merge($stream, $user_info[$stream['user_id']]);
    }, $streams_filtrados);

    $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
    usort($streams_enriquecidos, function ($a, $b) use ($order) {
        if ($order == 'asc') {
            return $a['espectadores'] <=> $b['espectadores'];
        } else {
            return $b['espectadores'] <=> $a['espectadores'];
        }
    });

    echo json_encode($streams_enriquecidos, JSON_PRETTY_PRINT);
} elseif ($http_code == 400) {
    http_response_code($http_code);
    echo json_encode(["error" => "Invalid or missing 'limmit' parameter."]);
} elseif ($http_code == 401) {
    http_response_code($http_code);
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
} elseif ($http_code == 500) {
    http_response_code($http_code);
    echo json_encode(["error" => "Internal Server Error"]);
} else {
    http_response_code($http_code);
    echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
}
