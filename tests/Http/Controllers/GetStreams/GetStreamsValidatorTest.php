<?php

namespace Tests\Http\Controllers\GetStreams;

use App\Http\Controllers\GetStreams\GetStreamsValidator;
use Tests\TestCase;

class GetStreamsValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function validateAlwaysReturnsTrue()
    {
        $validator = new GetStreamsValidator();
        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function validateAcceptsNullParameters()
    {
        $validator = new GetStreamsValidator();
        $this->assertTrue($validator->validate(null));
    }

    /**
     * @test
     */
    public function validateAcceptsEmptyArray()
    {
        $validator = new GetStreamsValidator();
        $this->assertTrue($validator->validate([]));
    }
}
