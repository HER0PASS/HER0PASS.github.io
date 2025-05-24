<?php

namespace Integration\Services;

use App\Models\APISession;
use App\Services\TokenService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class TokenServiceIntegrationTest extends TestCase
{
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FakeDataBaseRepository();
        $this->service = new TokenService($this->repository);
    }

    /**
     * @test
     */
    public function givenValidUserCreatesSessionWithToken(): void
    {
        $email = 'user1@example.com';
        $apiKey = '6288f213b19339919569e8b43f1ad852';

        $session = $this->service->createToken($email, $apiKey);

        // Confirmar que devuelve una sesion
        $this->assertInstanceOf(APISession::class, $session);
        $this->assertNotEmpty($session->getToken());

        // Confirmar que la sesión fue almacenada en el repositorio fake
        $storedSession = $this->repository->getSessionByUserId($session->getUserId());
        $this->assertNotNull($storedSession);
        $this->assertEquals($session->getToken(), $storedSession->getToken());
    }

    /**
     * @test
     */
    public function givenValidUserWithExistingSessionReturnsSessionToken(): void
    {
        $email = 'user1@example.com';
        $apiKey = '6288f213b19339919569e8b43f1ad852';

        // Primera llamada: genera una sesión nueva
        $oldSession = $this->service->createToken($email, $apiKey);
        $this->assertNotNull($oldSession);

        // Comrpuebo que me devuelve el token
        $newSession = $this->service->createToken($email, $apiKey);
        $this->assertNotNull($newSession);

        //Verifico que son iguales
        $this->assertEquals($oldSession->getToken(), $newSession->getToken());
    }

    /**
     * @test
     */
    public function givenExpiredSessionGeneratesNewToken(): void
    {
        $email = 'user1@example.com';
        $apiKey = '6288f213b19339919569e8b43f1ad852';
        $userId = '1';

        // Simulo una sesión expirada para el usuario
        $expiredToken = 'expired_token';
        $expired_at = new \DateTime('-2 hours');

        $expiredSession = new APISession($userId, $expiredToken, $expired_at);
        $this->repository->storeSession($expiredSession);

        // Llamo a createToken, debería detectar expiración y generar nuevo token
        $newSession = $this->service->createToken($email, $apiKey);

        // Se devuelve nueva sesión con token diferente y activo
        $this->assertInstanceOf(APISession::class, $newSession);
        $this->assertNotEquals($expiredToken, $newSession->getToken());
        $this->assertFalse($newSession->isExpired());
    }

    /**
     * @test
     */
    public function givenInvalidEmailOrApiKeyReturnsNull(): void
    {
        $email = 'nonexistent@example.com';
        $apiKey = 'invalidapikey000000000000000000000000';

        $session = $this->service->createToken($email, $apiKey);

        $this->assertNull($session);
    }
}
