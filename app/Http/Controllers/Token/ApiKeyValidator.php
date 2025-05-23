<?php

namespace App\Http\Controllers\Token;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;

class ApiKeyValidator
{
    public function validateApiKey(?string $apiKey): string
    {
        if (empty($apiKey)) {
            throw new EmptyApiKeyException();
        }
        $apiKeyLength = strlen($apiKey);
        if (!preg_match('/^[a-zA-Z0-9]+$/', $apiKey) | $apiKeyLength != 32) {
            throw new InvalidApiKeyException();
        }
        return $apiKey;
    }
}
