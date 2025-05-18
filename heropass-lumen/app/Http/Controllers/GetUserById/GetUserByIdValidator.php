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
        // Verificar que exista el parámetro id
        $id = $request->input('id');
        if (!$id) {
            return [
                "isValid" => false,
                "error" => "Invalid or missing 'id' parameter.",
                "status" => 400
            ];
        }

        // Validar que el ID sea válido
        if (!$this->validate($id)) {
            return [
                "isValid" => false,
                "error" => "Invalid or missing 'id' parameter.",
                "status" => 400
            ];
        }

        // Verificar token de autorización
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return [
                "isValid" => false,
                "error" => "Unauthorized. Twitch access token is invalid or has expired.",
                "status" => 401
            ];
        }

        return [
            "isValid" => true,
            "token" => $matches[1],
            "id" => $id
        ];
    }

    public function verificarToken($token)
    {
        require_once base_path('public/endpoints/verificarToken.php');
        return verificarToken($token);
    }
}
