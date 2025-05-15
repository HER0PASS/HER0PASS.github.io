<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\TokenManager;
use App\Repository\DataBaseRepository;
use Tests\TestCase;

class TokenManagerTest extends TestCase
{
    /** Test */
    public function testGets200AndTokenWhenValidApiKeyAndEmailAreGiven2(): void
    {
        $email = 'notSanitazed@mail.com';
        $apiKey = 'a39f4e4a9fd2329b9d190e18e67e58c7';
        $expectedUserId = '12';

        // Creamos un mock del repositorio
        $mockRepository = $this->createMock(DataBaseRepository::class);

        // Definimos el comportamiento esperado
        $mockRepository->expects($this->once())
            ->method('checkUserExistence')
            ->with($email, $apiKey)
            ->willReturn($expectedUserId);

        // Creamos TokenManager con el mock
        $tokenManager = new TokenManager($mockRepository);

        // Act
        $actualUserId = $tokenManager->checkUser($email, $apiKey);

        // Assert
        $this->assertEquals($expectedUserId, $actualUserId);
    }
    public function testGetsExpiredDateAndTokenWhenGivingAUserId(): void
    {
        $userId = '2';
        $token = 'ab7ecdeaa06336505d1781576c805f47';
        $expectedExpiredDate = '2025-02-16 16:20:49';

        $mockRepository = $this->createMock(DataBaseRepository::class);

        $mockRepository->expects($this->once())
            ->method('getTokenFromDataBase')
            ->with($userId)
            ->willReturn($token);

        $mockRepository->expects($this->once())
            ->method('getExpireDate')
            ->with($token)
            ->willReturn($expectedExpiredDate);

        $tokenManager = new TokenManager($mockRepository);

        // Act: obtenemos el JsonResponse
        $response = $tokenManager->getToken($userId);

        // Extraemos los datos como array asociativo
        $data = $response->getData(true);

        // Assert
        $this->assertEquals($token, $data['token']);
        $this->assertEquals($expectedExpiredDate, $data['expires_at']);
    }
}
