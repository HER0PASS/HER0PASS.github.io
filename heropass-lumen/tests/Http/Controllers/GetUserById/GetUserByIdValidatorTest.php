<?php

namespace Tests\Http\Controllers\GetUserById;

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
    public function requestIsInvalidIfIdIsNotGiven()
    {
        $this->assertFalse($this->validator->validate(null));
    }

    /**
     * @test
     */
    public function requestIsInvalidIfIdIsMinorThanOne()
    {
        $this->assertFalse($this->validator->validate(0));
        $this->assertFalse($this->validator->validate(-1));
    }

    /**
     * @test
     */
    public function requestIsInvalidIfIdIsNotNumeric()
    {
        $this->assertFalse($this->validator->validate('abc'));
        $this->assertFalse($this->validator->validate([]));
        $this->assertFalse($this->validator->validate(new \stdClass()));
    }

    /**
     * @test
     */
    public function requestIsValidIfIdIsNumericAndGreaterThanZero()
    {
        $this->assertTrue($this->validator->validate(1));
        $this->assertTrue($this->validator->validate('1'));
        $this->assertTrue($this->validator->validate(100));
    }

    /**
     * @test
     */
    public function validateRequestReturnsErrorIfIdIsMissing()
    {
        $request = Request::create('/analytics/user', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid or missing 'id' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function validateRequestReturnsErrorIfIdIsInvalid()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => 'abc']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid 'id' parameter. Must be numeric and greater than or equal to 1.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function validateRequestReturnsErrorIfAuthorizationHeaderIsMissing()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '1']);

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Unauthorized. Twitch access token is invalid or has expired.", $result['error']);
        $this->assertEquals(401, $result['status']);
    }

    /**
     * @test
     */
    public function validateRequestReturnsSuccessWithValidData()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '1']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertTrue($result['isValid']);
        $this->assertEquals('valid-token', $result['token']);
        $this->assertEquals('1', $result['id']);
    }
}
