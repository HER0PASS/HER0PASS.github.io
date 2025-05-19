<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\TokenManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class AuthenticateTest extends TestCase
{
    private $tokenManager;
    private $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->middleware = new Authenticate($this->tokenManager);
    }

    /**
     * @test
     */
    public function gets401IfAuthorizationHeaderIsMisssing()
    {
        $request = Request::create('/endpointExample', 'GET');
        $response = $this->middleware->handle($request, fn () => response('OK'));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertStringContainsString('Token is invalid or expired', $response->getContent());
    }

    /**
     * @test
     */
    public function gets401IfAuthorizationHeaderIsInvalid()
    {
        $request = Request::create('/endpointExample', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer invalid_token'
        ]);

        $this->tokenManager
            ->method('tokenIsActive')
            ->with('invalid_token')
            ->willReturn(false);

        $response = $this->middleware->handle($request, fn () => response('OK'));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertStringContainsString('Token is invalid or expired', $response->getContent());
    }

    /**
     * @test
     */
    public function getsValidationIfAuthorizationHeaderIsValid()
    {
        $request = Request::create('/endpointExample', 'GET', [], [], [], [
            'HTTP_Authorization' => 'Bearer valid_token'
        ]);

        $this->tokenManager
            ->method('tokenIsActive')
            ->with('valid_token')
            ->willReturn(true);

        $response = $this->middleware->handle($request, fn () => response('Success'));

        $this->assertEquals('Success', $response->getContent());
    }
}
