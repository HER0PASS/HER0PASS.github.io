<?php

namespace Tests\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use App\Services\TokenService;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenServiceUnitaryTest extends TestCase
{
    private $repository;
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(DataBaseRepositoryInterface::class);
        $this->service = new TokenService($this->repository);
    }

    /**
     * @test
     */
    public function givenValidEmailAndApiKeyGeneratesValidToken(): void
    {
        $email = 'user@example.com';
        $apiKey = str_repeat('A', 32);
        $userId = 1;

        $user = new APIUser($userId, $email, $apiKey);

        $this->repository
            ->shouldReceive('getAPIUserByEmail')
            ->once()
            ->with(Mockery::type(APIUser::class))
            ->andReturn($user);

        $this->repository
            ->shouldReceive('getSessionByUserId')
            ->once()
            ->with($userId)
            ->andReturn(null);

        $this->repository
            ->shouldReceive('registerSession')
            ->once()
            ->with(Mockery::on(function (APISessions $session) {
                $token = $session->getToken();
                return strlen($token) > 0;
            }));

        $session = $this->service->createToken($email, $apiKey);

        $this->assertInstanceOf(APISessions::class, $session);
        $this->assertNotEmpty($session->getToken());
        $this->assertIsString($session->getToken());
    }

    /**
     * @test
     */
    public function givenExistingSessionUpdatesSessionToken(): void
    {
        $email = 'user@example.com';
        $apiKey = str_repeat('A', 32);
        $userId = 1;

        $user = new APIUser($userId, $email, $apiKey);
        $existingSession = new APISessions($userId);
        $existingSession->generateToken(); // token viejo

        $this->repository
            ->shouldReceive('getAPIUserByEmail')
            ->once()
            ->andReturn($user);

        $this->repository
            ->shouldReceive('getSessionByUserId')
            ->once()
            ->with($userId)
            ->andReturn($existingSession);

        $this->repository
            ->shouldReceive('updateSession')
            ->once()
            ->with(Mockery::on(function (APISessions $session) {
                return strlen($session->getToken()) > 0;
            }));

        $session = $this->service->createToken($email, $apiKey);

        $this->assertInstanceOf(APISessions::class, $session);
        $this->assertNotEmpty($session->getToken());
    }


    /**
     * @test
     */
    public function givenInvalidUserReturnsNull(): void
    {
        $email = 'invalid@example.com';
        $apiKey = str_repeat('B', 32);

        $this->repository
            ->shouldReceive('getAPIUserByEmail')
            ->once()
            ->andReturn(null);

        $session = $this->service->createToken($email, $apiKey);

        $this->assertNull($session);
    }
}
