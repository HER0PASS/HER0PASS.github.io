<?php

namespace App\Models;

class APIUser
{
    private string $id;
    private string $email;
    private string $api_key;


    public function __construct(string $id, string $email, string $api_key)
    {
        $this->id = $id;
        $this->email = $email;
        $this->api_key = $api_key;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiKey(): string
    {
        return $this->api_key;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'api_key' => $this->api_key
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
