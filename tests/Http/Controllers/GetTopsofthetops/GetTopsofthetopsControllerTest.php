<?php

namespace Http\Controllers\GetTopsofthetops;

use App\Http\Controllers\GetTopsofthetops\GetTopsofthetopsController;
use App\Http\Controllers\GetTopsofthetops\GetTopsofthetopsValidator;
use App\Services\GetTopsofthetopsService;
use Illuminate\Http\Request;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;
use Tests\TestCase;

class GetTopsofthetopsControllerTest extends TestCase
{
    private GetTopsofthetopsController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new GetTopsofthetopsValidator();
        $service = new GetTopsofthetopsService(new FakeDataBaseRepository(), new FakeTwitchApiRepository());

        $this->controller = new GetTopsofthetopsController($validator, $service);
    }

    /**
     * @test
     */
    public function givenInvalidSinceParameterReturns400(): void
    {
        // Enviar un valor no numérico, por ejemplo: 'abc'
        $request = Request::create('/analytics/topsofthetops', 'GET', ['since' => 'abc']);

        $response = $this->controller->getTopsofthetops($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid 'since' parameter.", $data['error']);
    }

    /**
     * @test
     */
    public function givenValidSinceReturnsTopsofthetops200(): void
    {
        $request = Request::create('/analytics/topsofthetops', 'GET', ['since' => 1]);

        $response = $this->controller->getTopsofthetops($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        $this->assertEquals('Dota', $data[0]['game_name']);
        $this->assertEquals('Elden Ring', $data[1]['game_name']);
        $this->assertEquals('PUBG', $data[2]['game_name']);
    }
}
