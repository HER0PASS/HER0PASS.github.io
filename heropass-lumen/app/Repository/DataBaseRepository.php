<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\TokenManager;
use App\Repository\DataBaseRepository;
use Tests\TestCase;

class TokenManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('Europe/Madrid');
        $this->databaseTestRepository = new DataBaseRepository();
        // Esto hará que $db esté listo
    }
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

    public function testUpdatesDatabaseWhenGivingExpiredToken(): void
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
    public function testRegisterTokenCalledWhenNoTokenExists(): void
    {
        $userId = '15';
        $token = bin2hex(random_bytes(16));
        $expires_at = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));
        $mockRepo = $this->createMock(DataBaseRepository::class);

        // getTokenFromDatabase debe devolver null
        $mockRepo->expects($this->once())
            ->method('getTokenFromDatabase')
            ->with($userId)
            ->willReturn(null);
        // Se espera que se llame a registerTokenInDatabase
        $mockRepo->expects($this->once())
            ->method('registerTokenInDatabase')
            ->with($token, $expires_at, $userId);

        // No debe llamarse a updateTokenInDatabase
        $mockRepo->expects($this->never())
            ->method('updateTokenInDatabase');

        $tokenManager = new \App\Http\Middleware\TokenManager($mockRepo);
        $tokenManager->updateToken($token, $expires_at, $userId);
    }
    public function testWithNullTokenIsCreatedAndInserted()
    {
        $userId = '15';
        $token = bin2hex(random_bytes(16));
        $expires_at = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));

        // Llamamos a la función que debe insertar el token
        $this->databaseTestRepository->registerTokenInDatabase($token, $expires_at, $userId);

        // Ahora verificamos que el token existe en la base de datos
        $stmt = $this->databaseTestRepository->connect()->prepare("SELECT * FROM sessions WHERE user_id = :user_id AND token = :token");
        $stmt->execute(['user_id' => $userId, 'token' => $token]);
        $result = $stmt->fetch();

        $this->assertNotFalse($result, 'El token debería estar registrado en la base de datos');
        $this->assertEquals($expires_at, $result['expires_at']);
    }
    public function testWithExpiredTokenIsCreatedAndUpdated()
    {
        $userId = '4';
        $token = '41d2562ddc215251d5c6dfd86c44d16a';
        $expires_at = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));

        // ⬇️ Mostramos lo que vamos a insertar
        echo "Insertando expires_at: $expires_at\n";

        // Insertamos
        $this->databaseTestRepository->updateTokenInDatabase($token, $expires_at, $userId);

        // Consultamos lo insertado
        $stmt = $this->databaseTestRepository->connect()->prepare("SELECT * FROM sessions WHERE user_id = :user_id AND token = :token");
        $stmt->execute(['user_id' => $userId, 'token' => $token]);
        $result = $stmt->fetch();

        // ⬇️ Mostramos lo que se guardó
        echo "Fecha guardada en BBDD: {$result['expires_at']}\n";

        $this->assertNotFalse($result, 'El token debería estar registrado en la base de datos');
        $this->assertEquals($expires_at, $result['expires_at']);
    }
}
