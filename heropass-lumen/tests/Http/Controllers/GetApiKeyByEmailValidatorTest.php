<?php

namespace Tests\Http\Controllers;

use App\Exceptions\EmptyEmailException;
use App\Http\Controllers\GetApiKeyByEmailValidator;
use PHPUnit\Framework\TestCase;

class GetApiKeyByEmailValidatorTest extends TestCase
{
    /** Test */
    public function testValidateGivenNoEmailThrowsException(): void{
        $validator = new GetApiKeyByEmailValidator();

        $this->expectException(EmptyEmailException::class);
        $this->expectExceptionMessage('Invalid parameter, email is required');

        $validator->validate(null);
    }
}
