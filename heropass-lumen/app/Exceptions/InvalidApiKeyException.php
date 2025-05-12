<?php

namespace App\Exceptions;

class InvalidApiKeyException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid API key format');
    }
}
