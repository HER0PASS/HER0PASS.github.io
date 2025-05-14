<?php

namespace App\Repository;

use App\Config\Config;
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
    public function updateApiKey(string $email, string $api_key): void
    {
        $stmt = $this->db->prepare("UPDATE users SET api_key = :api_key WHERE email = :email");

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
        $stmt->execute();
    }
    public function registerEmailAndApiKey(string $email, string $api_key): void
    {
        $stmt = $this->db->prepare("INSERT INTO users (email, api_key) VALUES (:email, :api_key)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':api_key', $api_key);
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
            throw new \Exception('No se pudo establecer la conexión con la base de datos.');
        }

        return $this->db;
    }
}
