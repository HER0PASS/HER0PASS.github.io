<?php

namespace App\Models;

class TwitchGetTopsofthetops
{
    private string $game_id;

    private string $game_name;

    private string $user_name;

    private int $total_videos;

    private int $total_views;

    private string $most_viewed_title;

    private int $most_viewed_views;

    private string $most_viewed_duration;

    private string $most_viewed_created_at;

    public function __construct(
        string $game_id,
        string $game_name,
        string $user_name,
        int $total_videos,
        int $total_views,
        string $most_viewed_title,
        int $most_viewed_views,
        string $most_viewed_duration,
        string $most_viewed_created_at
    ) {
        $this->game_id = $game_id;
        $this->game_name = $game_name;
        $this->user_name = $user_name;
        $this->total_videos = $total_videos;
        $this->total_views = $total_views;
        $this->most_viewed_title = $most_viewed_title;
        $this->most_viewed_views = $most_viewed_views;
        $this->most_viewed_duration = $most_viewed_duration;
        $this->most_viewed_created_at = $most_viewed_created_at;
    }

    public function getGameId(): string
    {
        return $this->game_id;
    }

    public function getGameName(): string
    {
        return $this->game_name;
    }

    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function getTotalVideos(): int
    {
        return $this->total_videos;
    }

    public function getTotalViews(): int
    {
        return $this->total_views;
    }

    public function getMostViewedTitle(): string
    {
        return $this->most_viewed_title;
    }

    public function getMostViewedDuration(): string
    {
        return $this->most_viewed_duration;
    }

    public function getMostViewedViews(): int
    {
        return $this->most_viewed_views;
    }

    public function getMostViewedCreatedAt(): string
    {
        return $this->most_viewed_created_at;
    }

    public function toArray(): array
    {
        return [
            'game_id' => $this->game_id,
            'game_name' => $this->game_name,
            'user_name' => $this->user_name,
            'total_videos' => $this->total_videos,
            'total_views' => $this->total_views,
            'most_viewed_title' => $this->most_viewed_title,
            'most_viewed_views' => $this->most_viewed_views,
            'most_viewed_duration' => $this->most_viewed_duration,
            'most_viewed_created_at' => $this->most_viewed_created_at
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['game_id'],
            $data['game_name'],
            $data['user_name'],
            $data['total_videos'],
            $data['total_views'],
            $data['most_viewed_title'],
            $data['most_viewed_views'],
            $data['most_viewed_duration'],
            $data['most_viewed_created_at']
        );
    }
}
