<?php
header("Access-Control-Allow-Origin: *");   // Permitir peticiones desde cualquier dominio
header("Content-Type: application/json");

// Parámetros de autenticación de la API de Twitch
$client_id = "ynbfszlhhuo6irowc6zuqlzu8g0f2t";
$access_token = "32tleomygkitibffxh0l0xc1ag2dmw";

// Obtener el ID del usuario desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Invalid or missing 'id' parameter."]);
    exit;
}

$user_id = $_GET['id'];
$api_url = "https://api.twitch.tv/helix/users?id=$user_id";

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

// Manejo de respuestas según código HTTP
if ($http_code == 200) {
    $data = json_decode($response, true);

    if (!isset($data["data"][0])) {
        echo json_encode(["error" => "User not found."]);
        exit;
    }

    // Filtrar la información relevante del streamer
    $streamer = $data["data"][0];
    $resultado = [
        "id" => $streamer["id"],
        "login" => $streamer["login"],
        "display_name" => $streamer["display_name"],
        "broadcaster_type" => $streamer["broadcaster_type"],
        "description" => $streamer["description"],
        "profile_image_url" => $streamer["profile_image_url"],
        "view_count" => $streamer["view_count"],
        "created_at" => $streamer["created_at"]
    ];

    echo json_encode($resultado, JSON_PRETTY_PRINT);
} elseif ($http_code == 401) {
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
} elseif ($http_code == 404) {
    echo json_encode(["error" => "User not found."]);
} elseif ($http_code == 500) {
    echo json_encode(["error" => "Internal Server Error"]);
} else {
    echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
}
?>
