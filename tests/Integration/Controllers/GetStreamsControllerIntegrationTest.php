<?php

namespace Tests\Unit\Controllers\GetStreams;

use App\Http\Controllers\GetStreams\GetStreamsController;
use App\Services\GetStreamsService;
use Illuminate\Http\Request;
use Mockery;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;
use Tests\TestCase;

class GetStreamsControllerIntegrationTest extends TestCase
{
    private GetStreamsController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $service = new GetStreamsService(new FakeTwitchApiRepository());
        $this->controller = new GetStreamsController($service);
    }

    /**
     * @test
     */
    public function givenValidTokenReturns200WithStreamsData()
    {
        $request = Request::create('/analytics/streams', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer valid-token'
        ]);

        $this->app->instance('request', $request);
        $response = $this->controller->getStreams();
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
    public function givenInvalidTokenReturns401Unauthorized()
    {
        $request = Request::create('/analytics/streams', 'GET', [], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token'
        ]);
        $this->app->instance('request', $request);

        // Mock que lanza TwitchApiException como si la API devolviera 401
        $mockRepo = \Mockery::mock(FakeTwitchApiRepository::class);
        $mockRepo->shouldReceive('getStreams')
            ->once()
            ->andThrow(new \App\Exceptions\TwitchApiException());

        $service = new \App\Services\GetStreamsService($mockRepo);
        $controller = new \App\Http\Controllers\GetStreams\GetStreamsController($service);

        $response = $controller->getStreams();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized. Twitch access token is invalid or has expired.', $data['error']);
    }
}
