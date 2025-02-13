<?php

require '../api/crearToken.php';

header("Access-Control-Allow-Origin: *"); // Permitir peticiones desde cualquier dominio
header("Content-Type: application/json");

// Obtener el parámetro 'since' de la solicitud
$since = isset($_GET['since']) ? (int)$_GET['since'] : null;

// Obtener el token
$credentials = obtenerToken();

// Manejo de errores si no se obtuvo el token
if (isset($credentials['error'])) {
    echo json_encode(["error" => "Failed to obtain access token", "details" => $credentials]);
    exit;
}

$client_id = $credentials['client_id'];
$access_token = $credentials['access_token'];

// Conectar a la base de datos (ejemplo usando PDO)
$host = 'db5017192845.hosting-data.io';
$db = 'dbs13808414';
$user = 'dbu2750275';
$pass = 'HeroPassPass1'; // Deja esto en blanco si no configuraste una contraseña para root

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la conexión: " . $e->getMessage()]);
    exit;
}

// Verificar si hay datos en caché
$query = $pdo->prepare("SELECT * FROM cache WHERE endpoint = 'topsofthetops' ORDER BY timestamp DESC LIMIT 1");
$query->execute();
$cache = $query->fetch(PDO::FETCH_ASSOC);

$use_cache = false;
if ($cache) {
    $cache_age = time() - strtotime($cache['timestamp']);
    if ($since === null && $cache_age < 600) {
        $use_cache = true;
    } elseif ($since !== null && $cache_age < $since) {
        $use_cache = true;
    }
}

if ($use_cache) {
    // Devolver datos en caché
    echo $cache['data'];
} else {
    // Realizar una nueva consulta a la API de Twitch
    $api_url = "https://api.twitch.tv/helix/games/top?first=3";
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

        // Procesar y almacenar los datos en caché
        $juegos_filtrados = array_map(function ($game) {
            return [
                "id" => $game["id"] ?? null,
                "name" => $game["name"] ?? null,
                "box_art_url" => $game["box_art_url"] ?? null,
                "igbd_id" => $game["igbd_id"] ?? null
            ];
        }, $data["data"]);

        $resultados = [];
        foreach ($juegos_filtrados as $juego) {
            if ($juego["id"]) {
                $videos_url = "https://api.twitch.tv/helix/videos?game_id=" . $juego["id"] . "&sort=views&first=40";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $videos_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $videos_response = curl_exec($ch);
                $videos_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($videos_http_code == 200) {
                    $videos_data = json_decode($videos_response, true);
                    $videos = $videos_data["data"] ?? [];

                    if (!empty($videos)) {
                        $usuarios = [];
                        foreach ($videos as $video) {
                            $user_name = $video["user_name"];
                            if (!isset($usuarios[$user_name])) {
                                $usuarios[$user_name] = [
                                    "total_videos" => 0,
                                    "total_views" => 0,
                                    "most_viewed" => $video
                                ];
                            }
                            $usuarios[$user_name]["total_videos"]++;
                            $usuarios[$user_name]["total_views"] += $video["view_count"];
                            if ($video["view_count"] > $usuarios[$user_name]["most_viewed"]["view_count"]) {
                                $usuarios[$user_name]["most_viewed"] = $video;
                            }
                        }

                        $top_user = array_reduce(array_keys($usuarios), function ($carry, $user_name) use ($usuarios) {
                            if (!$carry || $usuarios[$user_name]["most_viewed"]["view_count"] > $usuarios[$carry]["most_viewed"]["view_count"]) {
                                return $user_name;
                            }
                            return $carry;
                        });

                        $resultados[] = [
                            "game_id" => $juego["id"],
                            "game_name" => $juego["name"],
                            "user_name" => $top_user,
                            "total_videos" => $usuarios[$top_user]["total_videos"],
                            "total_views" => $usuarios[$top_user]["total_views"],
                            "most_viewed_title" => $usuarios[$top_user]["most_viewed"]["title"],
                            "most_viewed_views" => $usuarios[$top_user]["most_viewed"]["view_count"],
                            "most_viewed_duration" => $usuarios[$top_user]["most_viewed"]["duration"],
                            "most_viewed_created_at" => $usuarios[$top_user]["most_viewed"]["created_at"]
                        ];
                    }
                }
            }
        }

        // Almacenar los datos en caché
        $cache_data = json_encode($resultados, JSON_PRETTY_PRINT);
        $query = $pdo->prepare("INSERT INTO cache (endpoint, data, timestamp) VALUES ('topsofthetops', :data, NOW())");
        $query->execute(['data' => $cache_data]);

        // Devolver los datos
        echo $cache_data;
    } else {
        // Manejo de errores
        if ($http_code == 401) {
            echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
        } elseif ($http_code == 500) {
            echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
        } else {
            echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
        }
    }
}
?>