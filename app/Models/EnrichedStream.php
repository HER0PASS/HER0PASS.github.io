<?php

namespace App\Models;

class EnrichedStream
{
    private string $stream_id;
    private string $user_id;
    private string $user_name;
    private int $viewer_count;
    private string $title;
    private string $user_display_name;
    private string $profile_image_url;

    public function __construct(
        string $stream_id,
        string $user_id,
        string $user_name,
        int $viewer_count,
        string $title,
        string $user_display_name,
        string $profile_image_url
    ) {
        $this->stream_id = $stream_id;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->viewer_count = $viewer_count;
        $this->title = $title;
        $this->user_display_name = $user_display_name;
        $this->profile_image_url = $profile_image_url;
    }

    public function getStreamId(): string
    {
        return $this->stream_id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function getViewerCount(): int
    {
        return $this->viewer_count;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function fromRawData(array $stream, array $user): EnrichedStream
    {
        return new self(
            $stream['id'],
            $stream['user_id'],
            $stream['user_login'],
            $stream['viewer_count'],
            $stream['title'],
            $user['user_display_name'],
            $user['profile_image_url']
        );
    }

    public static function buildUserMap(array $users): array
    {
        $map = [];
        foreach ($users as $user) {
            $map[$user['id']] = [
                'user_display_name' => $user['display_name'],
                'profile_image_url' => $user['profile_image_url']
            ];
        }
        return $map;
    }

    public static function enrichStreams(array $streams, array $userMap): array
    {
        return array_map(function ($stream) use ($userMap) {
            $user = $userMap[$stream['user_id']] ?? ['user_display_name' => '', 'profile_image_url' => ''];
            return [
                'stream_id' => $stream['id'],
                'user_id' => $stream['user_id'],
                'user_name' => $stream['user_login'],
                'viewer_count' => $stream['viewer_count'],
                'title' => $stream['title'],
                'user_display_name' => $user['user_display_name'],
                'profile_image_url' => $user['profile_image_url']
            ];
        }, $streams);
    }
}
