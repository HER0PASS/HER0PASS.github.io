<?php

namespace Tests\Unit\Validators;

use App\Http\Controllers\GetEnrichedStreams\GetEnrichedStreamsValidator;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetEnrichedStreamsValidatorTest extends TestCase
{
    private GetEnrichedStreamsValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetEnrichedStreamsValidator();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function givenInvalidLimitFormatReturnsFalse()
    {
        $request = Request::create('/testEnriched', 'GET', ['limit' => 'a']);

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid 'limit' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenRequestWithMissingLimitReturnsError()
    {
        $request = Request::create('/testEnriched', 'GET');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid 'limit' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenRequestWithMinorLimitReturnsError()
    {
        $request = Request::create('/testEnriched', 'GET', ['limit' => '0']);

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid 'limit' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenRequestWithMajorLimitReturnsError()
    {
        $request = Request::create('/testEnriched', 'GET', ['limit' => '11']);

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid 'limit' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenValidRequestReturnsValidResponse()
    {
        $request = Request::create('/testEnriched', 'GET', ['limit' => '1']);

        $result = $this->validator->validateRequest($request);

        $this->assertTrue($result['isValid']);
        $this->assertEquals('1', $result['limit']);
    }
}
