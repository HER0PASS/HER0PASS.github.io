<?php

namespace App\Http\Controllers\GetTopsofthetops;

use Illuminate\Http\Request;

class GetTopsofthetopsValidator
{
    public function validate($since): bool
    {
        // El since debe ser numérico y mayor o igual a 1
        return is_numeric($since) && intval($since) >= 1;
    }

    public function validateRequest(Request $request): array
    {
        // Verificar que exista el parametro since y que sea valido
        $since = $request->input('since');
        if (!$this->validate($since)) {
            return [
                "isValid" => false,
                "error" => "Invalid 'since' parameter.",
                "status" => 400
            ];
        }

        return [
            "isValid" => true,
            "since" => $since
        ];
    }
}
