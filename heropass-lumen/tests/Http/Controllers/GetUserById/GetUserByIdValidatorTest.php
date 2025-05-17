<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use Tests\TestCase;

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

        $this->assertFalse($validator->validate('0'));
    }

    /**
     * @test
     */
    public function requestIsValidIfIdIsAtLeastOne()
    {
        $validator = new GetUserByIdValidator();

        $this->assertTrue($validator->validate('1'));
    }

    /**
     * @test
     */
    public function requestIsInvalidIfIdIsNotNumeric()
    {
        $validator = new GetUserByIdValidator();

        $this->assertFalse($validator->validate('abc'));
    }
}
