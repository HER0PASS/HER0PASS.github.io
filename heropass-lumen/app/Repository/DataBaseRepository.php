<?php

namespace App\Repository;

use PDO;
use PDOException;

class DataBaseRepository
{
    private ?PDO $db = null;

    public function getUserByEmail($email): string
    {
        $this->getConnection();

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return '';
        }
        return json_encode($user);
    }

    public function checkUserExistence($email, $api_key): ?string
    {
        $this->getConnection();

        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND api_key = :api_key");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return null;
        }
        return $user['id'];
    }

    public function updateApiKey(string $email, string $api_key): void
    {
        $this->getConnection();

        $stmt = $this->db->prepare("UPDATE users SET api_key = :api_key WHERE email = :email");

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
        $stmt->execute();
    }

    public function registerEmailAndApiKey(string $email, string $api_key): void
    {
        $this->getConnection();

        $stmt = $this->db->prepare("INSERT INTO users (email, api_key) VALUES (:email, :api_key)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
        $stmt->execute();
    }

    public function getExpireDate($token): ?string
    {
        $this->getConnection();

        $stmt = $this->db->prepare("SELECT expires_at FROM sessions WHERE token LIKE :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['expires_at'] : null;
    }

    public function getTokenFromDatabase($userId): ?string
    {
        $this->getConnection();

        $stmt = $this->db->prepare("SELECT token FROM sessions WHERE user_id LIKE :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['token'] : null;
    }

    public function registerTokenInDatabase($token, $expires_at, $userId): void
    {
        $this->getConnection();

        $stmt = $this->db->prepare("INSERT INTO sessions (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();
    }

    public function updateTokenInDatabase($token, $expires_at, $userId): void
    {
        $this->getConnection();

        $stmt = $this->db->prepare("UPDATE sessions SET token = :token, expires_at = :expires_at WHERE user_id = :user_id");

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expires_at);
        $stmt->execute();
    }

    public function connect(): ?PDO
    {
        $host     = env('DB_HOST');
        $port     = env('DB_PORT');
        $dbname   = env('DB_DATABASE');
        $user     = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (\PDOException $e) {
            // Error de conexión
            error_log('Connection failed: ' . $e->getMessage());
            return null;
        }
    }

    private function getConnection(): ?PDO
    {
        if ($this->db === null) {
            $this->db = $this->connect();
        }

        if ($this->db === null) {
            throw new \Exception('Database connection failed');
        }

        return $this->db;
    }
}
