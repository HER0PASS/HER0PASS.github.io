<?php

namespace Tests\Http\Controllers;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\RegisterValidator;
use PHPUnit\Framework\TestCase;

class RegisterValidatorTest extends TestCase
{
    /** Test */
    public function testValidateGivenNoEmailThrowsException(): void
    {
        $validator = new RegisterValidator();

        $this->expectException(EmptyEmailException::class);
        $this->expectExceptionMessage('The email is mandatory');

        $validator->validate(null);
    }

    public function testRequestIsInvalidateIfEmailAddressGivenIsNotValid(): void
    {
        $validator = new RegisterValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        $validator->validate('testNotValid');
    }

    public function testGivenNotSanitazedMailReturnsSanitazedEmail(): void
    {
        $validator = new RegisterValidator();
        $response = $validator->validate('(notSanitazed@mail.com)');
        $this->assertEquals('notSanitazed@mail.com', $response);
    }
}
