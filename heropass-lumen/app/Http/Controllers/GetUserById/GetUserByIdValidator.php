<?php

namespace App\Http\Controllers\GetUserById;

class GetUserByIdValidator
{

    public function validate($id): bool
    {
        // El ID debe ser numérico y mayor o igual a 1
        return is_numeric($id) && intval($id) >= 1;
    }
}
