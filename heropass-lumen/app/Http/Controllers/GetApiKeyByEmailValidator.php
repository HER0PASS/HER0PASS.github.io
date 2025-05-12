<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyEmailException;

class GetApiKeyByEmailValidator
{
    public function validate(?string $email): string
    {
        if (!isset($email)) {
            throw new EmptyEmailException('Invalid parameter, email is required');
        }
        return $email;
    }
}
