<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use PHPUnit\Framework\TestCase;

class GetUserByIdValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function requestIsInvalidIfIdIsNotGiven()
    {
        $validator = new GetUserByIdValidator();
        $this->assertFalse($validator->validate(null));
    }

    /**
     * @test
     */
    public function requestIsInvalidIfIdIsMinorThanOne()
    {
        $validator = new GetUserByIdValidator();
        $this->assertFalse($validator->validate(0));
        $this->assertFalse($validator->validate(-1));
    }

    /**
     * @test
     */
    public function requestIsInvalidIfIdIsNotNumeric()
    {
        $validator = new GetUserByIdValidator();
        $this->assertFalse($validator->validate('abc'));
        $this->assertFalse($validator->validate([]));
        $this->assertFalse($validator->validate(new \stdClass()));
    }

    /**
     * @test
     */
    public function requestIsValidIfIdIsNumericAndGreaterThanZero()
    {
        $validator = new GetUserByIdValidator();
        $this->assertTrue($validator->validate(1));
        $this->assertTrue($validator->validate('1'));
        $this->assertTrue($validator->validate(100));
    }
}
