<?php
// Configuración de la API de Twitch
$client_id = "ynbfszlhhuo6irowc6zuqlzu8g0f2t"; 
$access_token = "32tleomygkitibffxh0l0xc1ag2dmw"; 

// URL de la API de Twitch para obtener streams en vivo en español
$url = "https://api.twitch.tv/helix/streams?type=live&first=10&language=es";

// Configurar la solicitud HTTP con cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token",
    "Client-Id: $client_id"
]);

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
    header("Content-Type: application/json");
    echo json_encode($streams_filtrados, JSON_PRETTY_PRINT);
} elseif ($http_code == 401) {
    // Si la respuesta es 401, token inválido o expirado
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized. Twitch access token is invalid or has expired."]);
} elseif ($http_code == 500) {
    // Si la respuesta es 500, error interno del servidor
    header("Content-Type: application/json");
    echo json_encode(["error" => "Internal Server Error"]);
} else {
    // Manejo de otros códigos de error
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
}
?>
