<?php

namespace App\Models;

class TwitchUser
{
    private string $id;
    private string $login;
    private string $displayName;
    private string $type;
    private string $broadcasterType;
    private string $description;
    private string $profileImageUrl;
    private string $offlineImageUrl;
    private int $viewCount;
    private string $createdAt;

    public function __construct(
        string $id,
        string $login,
        string $displayName,
        string $type,
        string $broadcasterType,
        string $description,
        string $profileImageUrl,
        string $offlineImageUrl,
        int $viewCount,
        string $createdAt
    ) {
        $this->id = $id;
        $this->login = $login;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->broadcasterType = $broadcasterType;
        $this->description = $description;
        $this->profileImageUrl = $profileImageUrl;
        $this->offlineImageUrl = $offlineImageUrl;
        $this->viewCount = $viewCount;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBroadcasterType(): string
    {
        return $this->broadcasterType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getProfileImageUrl(): string
    {
        return $this->profileImageUrl;
    }

    public function getOfflineImageUrl(): string
    {
        return $this->offlineImageUrl;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'display_name' => $this->displayName,
            'type' => $this->type,
            'broadcaster_type' => $this->broadcasterType,
            'description' => $this->description,
            'profile_image_url' => $this->profileImageUrl,
            'offline_image_url' => $this->offlineImageUrl,
            'view_count' => $this->viewCount,
            'created_at' => $this->createdAt
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['login'],
            $data['display_name'],
            $data['type'],
            $data['broadcaster_type'],
            $data['description'],
            $data['profile_image_url'],
            $data['offline_image_url'],
            $data['view_count'],
            $data['created_at']
        );
    }
}
