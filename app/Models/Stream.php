<?php

namespace App\Models;

class Stream
{
    private string $title;
    private string $user_name;

    public function __construct(string $title, string $user_name)
    {
        $this->title = $title;
        $this->user_name = $user_name;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'user_name' => $this->user_name,
        ];
    }

    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['user_name'] ?? ''
        );
    }
}
