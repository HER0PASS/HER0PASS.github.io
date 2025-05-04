<?php

require __DIR__ . '/../api/crearToken.php';
include __DIR__ . '/../bbdd/conexion.php';
require __DIR__ . '/../verificarToken.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error."]);
    exit;
});

$headers = apache_request_headers();
if (
    !isset($headers['Authorization']) || !preg_match(
        '/Bearer\s(\S+)/',
        $headers['Authorization'],
        $matches
    )
) {
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

$credentials = obtenerToken();

if (isset($credentials['error'])) {
    http_response_code(401);
    echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
    exit;
}

$client_id = $credentials['client_id'];
$access_token = $credentials['access_token'];

$api_url = "https://api.twitch.tv/helix/streams?first=10";

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
            "title" => $stream["title"],
            "user_name" => $stream["user_name"],
        ];
    }, $data["data"]);

    echo json_encode($streams_filtrados, JSON_PRETTY_PRINT);
} elseif ($http_code == 401) {
    http_response_code($http_code);
    echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
} elseif ($http_code == 500) {
    http_response_code($http_code);
    echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
} else {
    http_response_code($http_code);
    echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
}
