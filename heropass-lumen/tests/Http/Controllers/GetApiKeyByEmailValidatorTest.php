<?php

namespace Tests\Http\Controllers;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\GetApiKeyByEmailValidator;
use PHPUnit\Framework\TestCase;

class GetApiKeyByEmailValidatorTest extends TestCase
{
    /** Test */
    public function testValidateGivenNoEmailThrowsException(): void
    {
        $validator = new GetApiKeyByEmailValidator();

        $this->expectException(EmptyEmailException::class);
        $this->expectExceptionMessage('Invalid parameter, email is required');

        $validator->validate(null);
    }

    public function testRequestIsInvalidateIfEmailAddressGivenIsNotValid(): void
    {
        $validator = new GetApiKeyByEmailValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The email given must be a valid address');

        $validator->validate('testNotValid');
    }

    public function testGivenNotSanitazedMailReturnsSanitazedEmail(): void
    {
        $validator = new GetApiKeyByEmailValidator();
        $response = $validator->validate('(notSanitazed@mail.com)');
        $this->assertEquals('notSanitazed@mail.com', $response);
    }
}
