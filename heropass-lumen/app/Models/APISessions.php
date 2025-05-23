<?php

namespace App\Models;

class APISessions
{
    private string $user_id;
    private string $token;

    private \DateTime $expires_at;

    public function __construct(string $user_id, string $token, \DateTime $expires_at)
    {
        $this->user_id = $user_id;
        $this->token = $token;
        $this->expires_at = $expires_at;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expires_at;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'token' => $this->token,
            'expires_at' => $this->expires_at
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'] ?? '',
            $data['token'] ?? '',
            new \DateTime($data['expires_at'] ?? 'now')
        );
    }
}
