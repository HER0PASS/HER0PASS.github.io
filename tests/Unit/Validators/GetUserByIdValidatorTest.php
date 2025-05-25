<?php

namespace Tests\Unit\Validators;

use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetUserByIdValidatorTest extends TestCase
{
    private GetUserByIdValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetUserByIdValidator();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function givenInvalidIdFormatReturnsFalse()
    {
        $this->assertFalse($this->validator->validate('abc'));
        $this->assertFalse($this->validator->validate([]));
        $this->assertFalse($this->validator->validate(new \stdClass()));
    }

    /**
     * @test
     */
    public function givenRequestWithMissingIdReturnsError()
    {
        $request = Request::create('/testUser', 'GET');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid or missing 'id' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenRequestWithInvalidIdReturnsError()
    {
        $request = Request::create('/testUser', 'GET', ['id' => '0']);

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid or missing 'id' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenValidRequestReturnsValidResponse()
    {
        $request = Request::create('/testUser', 'GET', ['id' => '1']);

        $result = $this->validator->validateRequest($request);

        $this->assertTrue($result['isValid']);
        $this->assertEquals('1', $result['id']);
    }
}
