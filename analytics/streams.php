<?php
header("Access-Control-Allow-Origin: *"); // Permitir peticiones desde cualquier dominio
header("Content-Type: application/json");

$client_id = "ynbfszlhhuo6irowc6zuqlzu8g0f2t"; 
$access_token = "32tleomygkitibffxh0l0xc1ag2dmw"; 
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

// Manejo de respuestas según código HTTP
if ($http_code == 200) {
    // Si la respuesta es exitosa, procesamos los datos
    $data = json_decode($response, true);

    if (!isset($data["data"])) {
        echo json_encode(["error" => "No se encontraron streams"]);
        exit;
    }

    // Filtramos los campos deseados
    $streams_filtrados = array_map(function ($stream) {
        return [
            "nombre" => $stream["user_name"],
            "title" => $stream["title"],
            "espectadores" => $stream["viewer_count"]
        ];
    }, $data["data"]);

    // Mostrar datos en formato JSON
    echo json_encode($streams_filtrados, JSON_PRETTY_PRINT);
} elseif ($http_code == 401) {
    // Si la respuesta es 401, token inválido o expirado
    echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
} elseif ($http_code == 500) {
    // Si la respuesta es 500, error interno del servidor
    echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
} else {
    // Manejo de otros códigos de error
    echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
}
?>