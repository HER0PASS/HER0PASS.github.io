<?php
function obtenerToken() {
    $client_id = "ynbfszlhhuo6irowc6zuqlzu8g0f2t";
    $client_secret = "lgxp7ucxdggpaxqderwieugkxumhld";
    $grant_type = "client_credentials";

    $api_url = "https://id.twitch.tv/oauth2/token";
    $data = http_build_query([
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => $grant_type
    ]);

    $headers = [
        "Content-Type: application/x-www-form-urlencoded",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decodificar la respuesta
    $response_data = json_decode($response, true);

    // Manejo de errores
    if ($http_code !== 200 || !isset($response_data['access_token'])) {
        return ["error" => "Failed to obtain access token", "http_code" => $http_code, "response" => $response_data];
    }

    return ["client_id" => $client_id, "access_token" => $response_data['access_token']];
}
?>
