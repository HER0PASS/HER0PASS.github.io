<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;
use Illuminate\Http\JsonResponse;

class TokenValidator
{
    public function validateEmail(?string $email): string
    {
        if (empty($email)) {
            throw new EmptyEmailException();
        }
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailAddressException();
        }
        return $email;
    }

    public function validateApiKey(?string $apiKey): string
    {
        if (empty($apiKey)) {
            throw new EmptyApiKeyException();
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $apiKey)) {
            throw new InvalidApiKeyException();
        }
        return $apiKey;
    }
}
