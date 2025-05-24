<?php

namespace App\Http\Controllers\GetUserById;

use Illuminate\Http\Request;

class GetUserByIdValidator
{
    public function validate($id): bool
    {
        // El ID debe ser numérico y mayor o igual a 1
        return is_numeric($id) && intval($id) >= 1;
    }

    public function validateRequest(Request $request): array
    {
        // Verificar que exista el parametro id y que sea valido
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
