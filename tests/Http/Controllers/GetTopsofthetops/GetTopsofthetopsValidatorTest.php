<?php

namespace Http\Controllers\GetTopsofthetops;

use App\Http\Controllers\GetTopsofthetops\GetTopsofthetopsValidator;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetTopsofthetopsValidatorTest extends TestCase
{
    private GetTopsofthetopsValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new GetTopsofthetopsValidator();
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
    public function givenRequestWithInvalidIdReturnsError()
    {
        $request = Request::create('/analytics/topsofthetops', 'GET', ['since' => '-1']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertFalse($result['isValid']);
        $this->assertEquals(400, $result['status']);
    }

    /**
     * @test
     */
    public function givenValidRequestReturnsValidResponse()
    {
        $request = Request::create('/analytics/topsofthetops', 'GET', ['since' => '1']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        $result = $this->validator->validateRequest($request);

        $this->assertTrue($result['isValid']);
        $this->assertEquals('1', $result['since']);
    }
}
