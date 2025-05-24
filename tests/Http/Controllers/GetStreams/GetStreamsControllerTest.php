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

    protected function setUp(): void
    {
        parent::setUp();

        $service = new GetStreamsService(new FakeDataBaseRepository(), new FakeTwitchApiRepository());
        $this->controller = new GetStreamsController($service);
    }

    /**
     * @test
     */
    public function givenValidTokenReturns200()
    {
        $request = Request::create('/analytics/streams', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer valid_token']);
        $response = $this->controller->getStreams($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401()
    {
        $request = Request::create('/analytics/streams', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalid_token']);
        $response = $this->controller->getStreams($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function givenNoTokenReturns401()
    {
        $request = Request::create('/analytics/streams', 'GET');
        $response = $this->controller->getStreams($request);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
