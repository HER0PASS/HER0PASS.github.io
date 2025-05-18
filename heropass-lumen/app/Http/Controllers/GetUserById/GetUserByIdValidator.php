<?php

namespace App\Http\Controllers\GetUserById;

class GetUserByIdValidator
{
    /**
     * Valida que el ID proporcionado sea válido
     * 
     * @param mixed $id El ID a validar
     * @return bool True si el ID es válido, false en caso contrario
     */
    public function validate($id): bool
    {
        // El ID debe ser numérico y mayor o igual a 1
        return is_numeric($id) && intval($id) >= 1;
    }
}
