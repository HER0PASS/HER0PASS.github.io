<?php

namespace Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\Register\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function testValidateGivenNoEmailThrowsException(): void
    {
        $validator = new EmailValidator();

        $this->expectException(EmptyEmailException::class);
        $this->expectExceptionMessage('The email is mandatory');

        $validator->validateEmail(null);
    }

    /**
     * @test
     */
    public function testRequestIsInvalidateIfEmailAddressGivenIsNotValid(): void
    {
        $validator = new EmailValidator();

        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        $validator->validateEmail('testNotValid');
    }

    /**
     * @test
     */
    public function testGivenNotSanitazedMailReturnsSanitazedEmail(): void
    {
        $validator = new EmailValidator();
        $response = $validator->validateEmail('(notSanitazed@mail.com)');
        $this->assertEquals('notSanitazed@mail.com', $response);
    }
}
