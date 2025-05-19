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
    public function requestIsInvalidIfIdIsNotNumeric()
    {
        $this->assertFalse($this->validator->validate('abc'));
        $this->assertFalse($this->validator->validate([]));
        $this->assertFalse($this->validator->validate(new \stdClass()));
    }

    /**
     * @test
     */
    public function getsErrorIfIdIsMissing()
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
    public function getsErrorIfIdIsInvalid()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '0']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals("Invalid or missing 'id' parameter.", $result['error']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function getsSuccessWithValidData()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '1']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertTrue($result['isValid']);
        $this->assertEquals('1', $result['id']);
        // Ya no se espera la clave 'token'
        $this->assertArrayNotHasKey('token', $result);
    }
}
