<?php

namespace Tests\Unit\Services;

use App\Services\GetStreamsService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetStreamsServiceTest extends TestCase
{
    private GetStreamsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repo = new FakeDataBaseRepository();
        $api = new FakeTwitchApiRepository();

        $this->service = new GetStreamsService($repo, $api);
    }

    /**
     * @test
     */
    public function gets500IfCredentialsError(): void
    {
        $repo = new FakeDataBaseRepository();
        $api = new FakeTwitchApiRepository();
        $service = $this->getMockBuilder(GetStreamsService::class)
            ->setConstructorArgs([$repo, $api])
            ->onlyMethods(['obtenerToken'])
            ->getMock();

        // Simular error al obtener el token
        $service->method('obtenerToken')->willReturn(['error' => 'Token fetch failed']);
        $response = $service->getStreamsData();

        // Verificar que se devuelve un error 500
        $this->assertEquals(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Internal server error.', $data['error']);
    }

    /**
     * @test
     */
    public function givenNoStreamsInDbReturnsFromApi(): void
    {
        // Crear un mock del repositorio DB que devuelve null (sin streams)
        $dbRepo = $this->createMock(FakeDataBaseRepository::class);
        $dbRepo->method('getStreams')->willReturn(null);

        // Usar el FakeTwitchApiRepository real para obtener streams
        $apiRepo = new FakeTwitchApiRepository();

        // Crear mock del servicio para simular un token válido
        $service = $this->getMockBuilder(GetStreamsService::class)
            ->setConstructorArgs([$dbRepo, $apiRepo])
            ->onlyMethods(['obtenerToken'])
            ->getMock();

        $service->method('obtenerToken')->willReturn([
            'client_id' => 'fake_client_id',
            'access_token' => 'fake_access_token'
        ]);

        $response = $service->getStreamsData();

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertCount(3, $data);
        $this->assertEquals('Analizando la nueva plantilla del FC Barcelona', $data[0]['title']);
        $this->assertEquals('messi', $data[0]['user_name']);
    }

    /**
     * @test
     */
    public function givenStreamsInDbReturnsFromDb(): void
    {
        // Usar el repositorio real que ya tiene streams
        $dbRepo = new FakeDataBaseRepository();

        // Crear un mock del repositorio API que nunca debería ser llamado
        $apiRepo = $this->createMock(FakeTwitchApiRepository::class);
        $apiRepo->expects($this->never())->method('getStreams');

        $service = new GetStreamsService($dbRepo, $apiRepo);
        $response = $service->getStreamsData();

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertCount(3, $data);
        $this->assertEquals('Analizando la nueva plantilla del FC Barcelona', $data[0]['title']);
        $this->assertEquals('messi', $data[0]['user_name']);
    }

    /**
     * @test
     */
    public function givenNoStreamsInDbAndNoneInApiReturns404(): void
    {
        // Crear un mock del repositorio DB que devuelve null (sin streams)
        $dbRepo = $this->createMock(FakeDataBaseRepository::class);
        $dbRepo->method('getStreams')->willReturn(null);

        // Crear un mock del repositorio API que también devuelve null (sin streams)
        $apiRepo = $this->createMock(FakeTwitchApiRepository::class);
        $apiRepo->method('getStreams')->willReturn(null);

        // Crear mock del servicio para simular un token válido
        $service = $this->getMockBuilder(GetStreamsService::class)
            ->setConstructorArgs([$dbRepo, $apiRepo])
            ->onlyMethods(['obtenerToken'])
            ->getMock();

        $service->method('obtenerToken')->willReturn([
            'client_id' => 'fake_client_id',
            'access_token' => 'fake_access_token'
        ]);

        $response = $service->getStreamsData();

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Streams not found.', $data['error']);
    }
}
