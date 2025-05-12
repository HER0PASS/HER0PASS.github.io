<?php

namespace App\Exceptions;

class InvalidEmailAddressException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The email given must be a valid address');
    }
}
