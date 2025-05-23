<?php

namespace App\Models;

class APISessions
{
    private ?int $user_id = null;
    private string $token;

    private \DateTime $expires_at;

    public function __construct(?int $user_id, ?string $token = null, ?\DateTime $expires_at = null)
    {
        $this->user_id = $user_id;
        $this->token = $token ?? '';
        $this->expires_at = $expires_at ?? new \DateTime();
    }

    public function generateToken(): void
    {
        $this->setToken(bin2hex(random_bytes(32)));
        $this->setExpiresAt((new \DateTime())->add(new \DateInterval('P3D')));
    }

    public function getUserId(): ?int
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

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setExpiresAt(\DateTime $expires_at): void
    {
        $this->expires_at = $expires_at;
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
