<?php

namespace App\Http\Controllers\GetUserById;

class GetUserByIdValidator
{
    public function validate($id)
    {
        return !is_null($id) && is_numeric($id) && intval($id) >= 1;
    }
}
