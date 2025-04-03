<?php

require '../api/crearToken.php';
include '../bbdd/conexion.php';
require '../verificarToken.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$headers = apache_request_headers();
if (!isset($headers['Authorization']) || !preg_match('/Bearer\s(\S+)/',
        $headers['Authorization'], $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Authorization header missing or invalid"]);
    exit;
}

$token = $matches[1];
$user_id = verificarToken($token);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

if (!isset($_GET['since'])) {
    $since = 600;
} else {
    $since = (int)$_GET['since'];
}

$credentials = obtenerToken();

if (isset($credentials['error'])) {
    echo json_encode(["error" => "Failed to obtain access token for Twitch", "details" => $credentials]);
    exit;
}

$client_id = $credentials['client_id'];
$access_token = $credentials['access_token'];

$query = $conn->prepare("SELECT * FROM cache WHERE endpoint = 'topsofthetops' ORDER BY timestamp DESC LIMIT 1");
$query->execute();
$result = $query->get_result();
$cache = $result->fetch_assoc();

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
    echo $cache['data'];
} else {
    $query = $conn->prepare("DELETE FROM cache");
    $query->execute();

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
                            if (
                                !$carry || $usuarios[$user_name]["most_viewed"]["view_count"] >
                                $usuarios[$carry]["most_viewed"]["view_count"]
                            ) {
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

        $cache_data = json_encode($resultados, JSON_PRETTY_PRINT);
        $query = $conn->prepare("INSERT INTO cache (endpoint, data, timestamp) VALUES ('topsofthetops', ?, NOW())");
        $query->bind_param('s', $cache_data);
        $query->execute();

        echo $cache_data;
    } elseif ($http_code == 401) {
        http_response_code(401);
        echo json_encode(["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired."]);
    } elseif ($http_code == 500) {
        http_response_code(500);
        echo json_encode(["error" => "RESPONSE 500: Internal Server Error"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unexpected error", "status" => $http_code]);
    }

}
$conn->close();
