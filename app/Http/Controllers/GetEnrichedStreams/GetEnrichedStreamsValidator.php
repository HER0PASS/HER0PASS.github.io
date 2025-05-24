<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use Illuminate\Http\Request;

class GetEnrichedStreamsValidator
{
    public function validate($id): bool
    {
        return is_numeric($id) && intval($id) >= 1;
    }

    public function validateRequest(Request $request): array
    {
        $id = $request->input('id');
        if (!$id || !$this->validate($id)) {
            return [
                "isValid" => false,
                "error" => "Invalid or missing 'id' parameter.",
                "status" => 400
            ];
        }

        return [
            "isValid" => true,
            "id" => $id
        ];
    }
}
