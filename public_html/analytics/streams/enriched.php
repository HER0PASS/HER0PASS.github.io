<?php

require '../../api/crearToken.php';

header("Access-Control-Allow-Origin: *"); // Permitir peticiones desde cualquier dominio
header("Content-Type: application/json");

// Obtener el token
$credentials = obtenerToken();

// Manejo de errores si no se obtuvo el token
if (isset($credentials['error'])) {
    echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
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
            "user_id" => $stream["user_id"],
            "title" => $stream["title"],
            "espectadores" => $stream["viewer_count"]
        ];
    }, $data["data"]);

    // Obtener información adicional del usuario
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

    // Crear un array asociativo con la información del usuario
    $user_info = [];
    foreach ($user_data['data'] as $user) {
        $user_info[$user['id']] = [
            'display_name' => $user['display_name'],
            'profile_image_url' => $user['profile_image_url']
        ];
    }

    // Enriquecer los streams con la información del usuario
    $streams_enriquecidos = array_map(function ($stream) use ($user_info) {
        return array_merge($stream, $user_info[$stream['user_id']]);
    }, $streams_filtrados);

    // Ordenar los streams según el número de espectadores
    $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
    usort($streams_enriquecidos, function ($a, $b) use ($order) {
        if ($order == 'asc') {
            return $a['espectadores'] <=> $b['espectadores'];
        } else {
            return $b['espectadores'] <=> $a['espectadores'];
        }
    });

    // Mostrar datos en formato JSON
    echo json_encode($streams_enriquecidos, JSON_PRETTY_PRINT);
} elseif ($http_code == 400) {
    // Si la respuesta es 400, solicitud incorrecta
    echo json_encode(["error" => "RESPONSE 400: Bad Request"]);
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
