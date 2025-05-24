<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\TokenManager;
use App\Models\APISession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class AuthenticateIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeDataBaseRepository();
        $this->tokenManager = new TokenManager($this->repository);
        $this->authenticate = new Authenticate($this->tokenManager);
    }

    /**
     * @test
     */
    public function givenValidTokenAllowsAccessToProtectedRoute(): void
    {

        $request = Request::create('/any-url', 'GET');
        $request->headers->set('Authorization', 'Bearer valid_token');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => true]));
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getData()->success);
    }

    /**
     * @test
     */
    public function givenMissingTokenReturns401()
    {
        $request = Request::create('/any-url', 'GET');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => false]));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Token is invalid or expired.', $response->getData()->error);
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401()
    {
        $request = Request::create('/any-url', 'GET');
        $request->headers->set('Authorization', 'Bearer ivalid_token');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => false]));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Token is invalid or expired.', $response->getData()->error);
    }

    /**
     * @test
     */
    public function givenExpiredTokenReturns401(): void
    {

        $this->repository->storeSession(new APISession('1', 'expired_token', new \DateTime('-1 hour')));

        $request = Request::create('/any-url', 'GET');
        $request->headers->set('Authorization', 'Bearer expired_token');

        $response = $this->authenticate->handle($request, fn () => new JsonResponse(['success' => false]));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Token is invalid or expired.', $response->getData()->error);
    }
}
