<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use Illuminate\Http\Request;

class GetEnrichedStreamsValidator
{
    public function validate($limit): bool
    {
        return is_numeric($limit) && intval($limit) >= 1 && intval($limit) <= 10;
    }

    public function validateRequest(Request $request): array
    {
        $limit = $request->input('limit');

        if (!$limit || !$this->validate($limit)) {
            return [
                "isValid" => false,
                "error" => "Invalid 'limit' parameter.",
                "status" => 400
            ];
        }

        return [
            "isValid" => true,
            "limit" => $limit
        ];
    }
}
