<?php
  require '../api/crearToken.php';

  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json");

  // Verificar si 'id' está en la URL
  if (!isset($_GET['id']) || empty($_GET['id'])) {
      echo json_encode(["error" => "Invalid or missing 'id' parameter."]);
      exit;
  }

  // Obtener el token
  $credentials = obtenerToken();

  // Manejo de errores si no se obtuvo el token
  if (isset($credentials['error'])) {
      echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
      exit;
  }

  $client_id = $credentials['client_id'];
  $access_token = $credentials['access_token'];

  $user_id = $_GET['id'];
  $api_url = "https://api.twitch.tv/helix/users?id=$user_id";

  // Configurar encabezados
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

  // Manejo de respuestas
  if ($http_code == 200) {
      $data = json_decode($response, true);
      if (!isset($data["data"][0])) {
          echo json_encode(["error" => "User not found."]);
          exit;
      }

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
  } else {
      echo json_encode(["error" => "Unexpected error", "status" => $http_code, "response" => json_decode($response, true)]);
  }
?>
