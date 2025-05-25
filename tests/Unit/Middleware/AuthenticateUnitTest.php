<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\TokenManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthenticateUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenManager = Mockery::mock(TokenManager::class);
        $this->authenticate = new Authenticate($this->tokenManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }


    /**
     * @test
     */
    public function givenNoAuthorizationHeaderReturns401()
    {
        $request = Request::create('/some-protected-endpoint', 'GET');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => true]));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Token is invalid or expired.', $response->getData()->error);
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401()
    {
        $this->tokenManager
            ->shouldReceive('tokenIsActive')
            ->once()
            ->andReturn(false);

        $request = Request::create('/some-protected-endpoint', 'GET');
        $request->headers->set('Authorization', 'Bearer fake_token');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => true]));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Token is invalid or expired.', $response->getData()->error);
    }

    /**
     * @test
     */
    public function givenValidTokenGoNextMiddleware()
    {
        $this->tokenManager
            ->shouldReceive('tokenIsActive')
            ->once()
            ->andReturn(true);


        $request = Request::create('/some-protected-endpoint', 'GET');
        $request->headers->set('Authorization', 'Bearer valid_token');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['ok' => true]));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getData()->ok);
    }
}
