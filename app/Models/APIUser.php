<?php

namespace App\Models;

class APIUser
{
    private ?int $id = null;
    private string $email;
    private string $apiKey;


    public function __construct(?int $id, string $email, string $apiKey = '')
    {
        $this->email = $email;
        $this->id = $id;
        $this->apiKey = $apiKey;
    }

    public function generateApiKey(): void
    {
        $this->apiKey = bin2hex(random_bytes(16));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'api_key' => $this->apiKey
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['email'] ?? '',
            $data['api_key'] ?? ''
        );
    }
}
