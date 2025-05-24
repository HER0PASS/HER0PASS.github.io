<?php

namespace Tests\Unit\Validators;

use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\Register\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    private EmailValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new EmailValidator();
    }

    /**
     * @test
     */
    public function givenEmptyEmailReturnsEmptyEmailException(): void
    {
        $this->expectException(\App\Exceptions\EmptyEmailException::class);
        $this->expectExceptionMessage('The email is mandatory');

        $this->validator->validateEmail(null);
    }

    /**
     * @test
     */
    public function givenInvalidEmailReturnsInvalidEmailAddressException(): void
    {
        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        $this->validator->validateEmail('invalidEmail');
    }

    /**
     * @test
     */
    public function givenNotSanitazedMailReturnsSanitazedEmail(): void
    {
        $invalidEmail = '(notSanitazed@mail.com)';
        $this->assertEquals('notSanitazed@mail.com', $this->validator->validateEmail($invalidEmail));
    }
}
