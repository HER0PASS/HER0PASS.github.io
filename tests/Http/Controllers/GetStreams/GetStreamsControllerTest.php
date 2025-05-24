<?php

namespace Tests\Unit\Controllers\GetStreams;

use App\Http\Controllers\GetStreams\GetStreamsController;
use App\Services\GetStreamsService;
use Illuminate\Http\Request;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;
use Tests\TestCase;

class GetStreamsControllerTest extends TestCase
{
    private GetStreamsController $controller;
    private GetStreamsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new GetStreamsService(new FakeDataBaseRepository(), new FakeTwitchApiRepository());
        $this->controller = new GetStreamsController($this->service);
    }

    /**
     * @test
     */
    public function validTokenReturns200()
    {
        // Simulamos un request con un token válido
        $request = Request::create(
            '/analytics/streams',
            'GET',
            [],
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer valid_token']
        );

        // Ejecutamos el controlador
        $response = $this->controller->getStreams($request);
        $this->assertEquals(200, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        foreach ($data as $stream) {
            $this->assertArrayHasKey('title', $stream);
            $this->assertArrayHasKey('user_name', $stream);
        }
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401()
    {
        // no se
    }
}
