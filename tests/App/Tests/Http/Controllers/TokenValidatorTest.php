<?php

namespace App\Tests\Http\Controllers;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\Token\ApiKeyValidator;
use Tests\TestCase;

class TokenValidatorTest extends TestCase
{
    /** Test */
    public function testGets400WhenNoEmailIsGiven(): void
    {
        $validator = new ApiKeyValidator();

        $this->expectException(EmptyEmailException::class);
        $this->expectExceptionMessage('The email is mandatory');

        $validator->validateEmail(null);
    }

    /** Test */
    public function testGets400WhenNoApiKeyIsGiven(): void
    {
        $validator = new ApiKeyValidator();

        $this->expectException(EmptyApiKeyException::class);
        $this->expectExceptionMessage('API key is empty');

        $validator->validateApiKey(null);
    }

    /** Test */
    public function testGets400WhenEmailGivenIsInvalid(): void
    {
        $validator = new ApiKeyValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        $validator->validateEmail('bad_address_email');
    }

    /** Test */
    public function testGets400WhenApiKeyGivenIsInvalid(): void
    {
        $validator = new ApiKeyValidator();

        $this->expectException(InvalidApiKeyException::class);
        $this->expectExceptionMessage('Invalid API key format');

        $validator->validateApiKey('__invalid|ApiKey__');
    }
}
