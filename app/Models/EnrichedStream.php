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
}
