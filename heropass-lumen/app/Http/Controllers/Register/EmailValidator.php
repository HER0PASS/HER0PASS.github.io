<?php

namespace App\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;

class EmailValidator
{
    public function validate(?string $email): string
    {
        if (empty($email)) {
            throw new EmptyEmailException();
        }
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailAddressException();
        }
        return $sanitizedEmail;
    }
}
