<?php

namespace Tests\Integration\Controllers;

use App\Exceptions\TwitchApiException;
use App\Http\Controllers\GetEnrichedStreams\GetEnrichedStreamsController;
use App\Http\Controllers\GetEnrichedStreams\GetEnrichedStreamsValidator;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Services\GetEnrichedStreamsService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeTwitchApiRepository;

class GetEnrichedStreamsControllerTest extends TestCase
{
    private GetEnrichedStreamsController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new GetEnrichedStreamsValidator();
        $service = new GetEnrichedStreamsService(new FakeTwitchApiRepository());
        $this->controller = new GetEnrichedStreamsController($validator, $service);
    }

    /**
     * @test
     */
    public function givenExceptionThrownReturns401()
    {
        $request = Request::create('/integrationEnriched', 'GET', ['limit' => 2]);

        $mockRepo = \Mockery::mock(TwitchApiRepositoryInterface::class);
        $mockRepo->shouldReceive('getEnrichedStreams')
            ->once()
            ->with(2)
            ->andThrow(new TwitchApiException());

        $service = new GetEnrichedStreamsService($mockRepo);
        $controller = new GetEnrichedStreamsController(
            new GetEnrichedStreamsValidator(),
            $service
        );

        $response = $controller->getEnrichedStream($request);
        $this->assertEquals(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Internal Server Error', $data['error']);
    }

    /**
     * @test
     */
    public function givenValidRequestReturns200WithEnrichedStreams()
    {
        $request = Request::create('/integrationEnriched', 'GET', ['limit' => 3]);

        $response = $this->controller->getEnrichedStream($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        foreach ($data as $stream) {
            $this->assertArrayHasKey('title', $stream);
            $this->assertArrayHasKey('user_name', $stream);
            $this->assertArrayHasKey('user_display_name', $stream);
            $this->assertArrayHasKey('profile_image_url', $stream);
        }
    }
}
