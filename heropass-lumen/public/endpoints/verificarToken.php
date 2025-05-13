<?php

function verificarToken($token)
{
    require __DIR__ . '/bbdd/conexion.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE token = :token AND expires_at > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($session) {
            return $session['user_id'];
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return false;
    }
}
