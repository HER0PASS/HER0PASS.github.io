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

    // Obtener id del usuario a consultar
    $user_id = $_GET['id'];

    // Manejo de errores si no se obtuvo el token
    if (isset($credentials['error'])) {
      echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
      exit;
    }

    require '../bbdd/conexion.php';

    // Verificar si el usuario ya existe en la base de datos comparando con la columna idUser
    $sql = "SELECT * FROM twitchusers WHERE idUser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // SI EL USUARIO EXISTE, LO DEVOLVEMOS DE LA BDD
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc(); // Obtener el resultado
        echo json_encode(json_decode($user_data["data"]), JSON_PRETTY_PRINT);
        $conn->close(); // CERRAR CONEXIÓN ANTES DE SALIR
        exit; // DETENER EJECUCIÓN
    }

    // SI EL USUARIO NO EXISTE, CONSULTAMOS LA API Y LO GUARDAMOS EN LA BDD
    else{
        $client_id = $credentials['client_id'];
        $access_token = $credentials['access_token'];

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

            // insertar datos del usuario en la bdd
            $data_json = json_encode($resultado);
            $insert_sql = "INSERT INTO twitchusers (idUser, data) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $streamer["id"], $data_json);
            $insert_stmt->execute();

            echo json_encode($resultado, JSON_PRETTY_PRINT);
        } elseif ($http_code == 400) {
            // Si la respuesta es 400, invalid or missing parametro 'id'
            echo json_encode(["error" => "RESPONSE 400: Invalid or missing 'id' parameter."]);
        } elseif ($http_code == 401) {
            // Si la respuesta es 401, token inválido o expirado
            echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
        } elseif ($http_code == 404) {
            // Si la respuesta es 404, usuario no encontrado
            echo json_encode(["error" => "RESPONSE 404: User not found."]);
        } elseif ($http_code == 500) {
            // Si la respuesta es 500, error interno del servidor
            echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
        } else {
            // Manejo de otros códigos de error
            echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
        }
        // Cerrar la conexión
        $conn->close();
    }
?>
