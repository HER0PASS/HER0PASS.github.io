<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\TokenManager;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class TokenManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('Europe/Madrid');
    }

    /**
     * @test
     */
    public function testGets200AndTokenWhenValidApiKeyAndEmailAreGiven2(): void
    {
        $email = 'notSanitazed@mail.com';
        $apiKey = 'a39f4e4a9fd2329b9d190e18e67e58c7';
        $expectedUser = new APIUser('12', $email, $apiKey);

        $mockRepository = $this->createMock(DataBaseRepositoryInterface::class);
        $mockRepository->expects($this->once())
            ->method('checkAPIUserExistence')
            ->with($this->callback(function ($user) use ($email, $apiKey) {
                return $user instanceof APIUser &&
                    $user->getEmail() === $email &&
                    $user->getApiKey() === $apiKey;
            }))
            ->willReturn($expectedUser);

        $tokenManager = new TokenManager($mockRepository);

        $actualUser = $tokenManager->checkUser($email, $apiKey);

        $this->assertEquals($expectedUser, $actualUser);
    }

    /**
     * @test
     */
    public function testGetsExpiredDateAndTokenWhenGivingAUserId(): void
    {
        $userId = '2';
        $token = 'ab7ecdeaa06336505d1781576c805f47';
        $expectedExpiredDate = new \DateTime('2025-02-16 16:20:49');

        $mockRepository = $this->createMock(DataBaseRepositoryInterface::class);

        $session = new APISessions($userId, $token, $expectedExpiredDate);

        $mockRepository->expects($this->once())
            ->method('getSessionByUserId')
            ->with($userId)
            ->willReturn($session);

        $tokenManager = new TokenManager($mockRepository);

        $response = $tokenManager->getToken($userId);
        $data = $response->getData(true);

        $this->assertEquals($token, $data['token']);
        $this->assertEquals($expectedExpiredDate->format('Y-m-d H:i:s'), $data['expires_at']);
    }

    /**
     * @test
     */
    public function testUpdatesDatabaseWhenGivingExpiredToken(): void
    {
        $userId = '2';
        $token = 'ab7ecdeaa06336505d1781576c805f47';
        $expectedExpiredDate = new \DateTime('2025-02-16 16:20:49');

        $mockRepository = $this->createMock(DataBaseRepositoryInterface::class);
        $session = new APISessions($userId, $token, $expectedExpiredDate);

        $mockRepository->expects($this->once())
            ->method('getSessionByUserId')
            ->with($userId)
            ->willReturn($session);

        $tokenManager = new TokenManager($mockRepository);

        $response = $tokenManager->getToken($userId);
        $data = $response->getData(true);

        $this->assertEquals($token, $data['token']);
        $this->assertEquals($expectedExpiredDate->format('Y-m-d H:i:s'), $data['expires_at']);
    }

    /**
     * @test
     */
    public function testRegisterTokenCalledWhenNoTokenExists(): void
    {
        $userId = '15';
        $token = bin2hex(random_bytes(16));
        $expires_at = new \DateTime('+3 days');

        $mockRepo = $this->createMock(DataBaseRepositoryInterface::class);

        $mockRepo->expects($this->once())
            ->method('getSessionByUserId')
            ->with($userId)
            ->willReturn(null);

        $expectedSession = new APISessions($userId, $token, $expires_at);

        $mockRepo->expects($this->once())
            ->method('registerSession')
            ->with($expectedSession);

        $mockRepo->expects($this->never())
            ->method('updateSession');

        $tokenManager = new TokenManager($mockRepo);
        $tokenManager->updateToken($token, $expires_at, $userId);
    }

    /**
     * @test
     */
    public function testWithNullTokenIsCreatedAndInserted()
    {
        $repo = new FakeDataBaseRepository();

        $userId = '15';
        $token = bin2hex(random_bytes(16));
        $expires_at = new \DateTime('+3 days');

        $session = new APISessions($userId, $token, $expires_at);
        $repo->registerSession($session);

        $result = $repo->getSessionByUserId($userId);

        $this->assertNotNull($result, 'El token debería estar registrado en la base de datos');
        $this->assertEquals($token, $result->getToken());
        $this->assertEquals(
            $expires_at->format('Y-m-d H:i:s'),
            $result->getExpiresAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @test
     */
    public function testWithExpiredTokenIsCreatedAndUpdated()
    {
        $repo = new FakeDataBaseRepository();

        $userId = '4';
        $token = '41d2562ddc215251d5c6dfd86c44d16a';
        $oldDate = new \DateTime('2023-01-01 00:00:00');

        $oldSession = new APISessions($userId, $token, $oldDate);
        $repo->registerSession($oldSession);

        $newExpires = new \DateTime('+3 days');
        $updatedSession = new APISessions($userId, $token, $newExpires);
        $repo->updateSession($updatedSession);

        $result = $repo->getSessionByUserId($userId);

        $this->assertNotNull($result, 'La sesión actualizada debería existir');
        $this->assertEquals($token, $result->getToken());
        $this->assertEquals(
            $newExpires->format('Y-m-d H:i:s'),
            $result->getExpiresAt()->format('Y-m-d H:i:s')
        );
    }
}
