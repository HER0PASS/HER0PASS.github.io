<?php

namespace App\Exceptions;

class EmptyEmailException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
