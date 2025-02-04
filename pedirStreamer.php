<?php
header("Access-Control-Allow-Origin: *"); // Permitir peticiones desde cualquier dominio
header("Content-Type: application/json");

// Verificar si el formulario envió un nombre de streamer
if (!isset($_POST["streamer"]) || empty($_POST["streamer"])) {
    echo json_encode(["error" => "No se proporcionó el nombre del streamer"]);
    exit;
}

// Datos de autenticación de Twitch
$client_id = "ynbfszlhhuo6irowc6zuqlzu8g0f2t";  
$access_token = "32tleomygkitibffxh0l0xc1ag2dmw";  
$streamer_name = urlencode($_POST["streamer"]); // Escapar el nombre del streamer

// 1. Obtener el broadcaster_id del streamer
$api_url_user = "https://api.twitch.tv/helix/users?login=$streamer_name";
$headers = [
    "Client-ID: $client_id",
    "Authorization: Bearer $access_token"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url_user);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    echo json_encode(["error" => "Error al obtener el broadcaster_id", "status" => $http_code]);
    exit;
}

// Decodificar la respuesta de la API
$data = json_decode($response, true);

if (!isset($data["data"][0]["id"])) {
    echo json_encode(["error" => "Streamer no encontrado"]);
    exit;
}

// Extraer el broadcaster_id
$broadcaster_id = $data["data"][0]["id"];

// 2. Obtener información del canal del streamer
$api_url_channel = "https://api.twitch.tv/helix/channels?broadcaster_id=$broadcaster_id";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url_channel);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $channel_data = json_decode($response, true);
    
    if (!isset($channel_data["data"][0])) {
        echo json_encode(["error" => "No se encontró información del canal"]);
        exit;
    }

    // Extraer la información relevante
    $channel_info = [
        "broadcaster_id" => $broadcaster_id,
        "nombre" => $channel_data["data"][0]["broadcaster_name"],
        "titulo" => $channel_data["data"][0]["title"],
        "juego" => $channel_data["data"][0]["game_name"],
        "idioma" => $channel_data["data"][0]["broadcaster_language"]
    ];

    echo json_encode($channel_info, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "Error al obtener datos del canal", "status" => $http_code]);
}
?>
