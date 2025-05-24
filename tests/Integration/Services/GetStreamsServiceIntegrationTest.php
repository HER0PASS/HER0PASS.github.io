<?php

namespace Tests\Integration\Services;

use App\Exceptions\TwitchApiException;
use App\Models\Stream;
use App\Repository\TwitchAPIRepository;
use App\Services\GetStreamsService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeTwitchApiRepository;

class GetStreamsServiceIntegrationTest extends TestCase
{
    private GetStreamsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $apiRepository = new FakeTwitchApiRepository();
        $this->service = new GetStreamsService($apiRepository);
    }

    /**
     * @test
     */
    public function givenValidApiResponseReturnsStreamObjects(): void
    {
        $credentials = [
            'client_id' => 'fake-client-id',
            'access_token' => 'fake-access-token',
        ];

        $repo = $this->getMockBuilder(TwitchApiRepository::class)
            ->setConstructorArgs([$credentials])
            ->onlyMethods(['getApiResponse'])
            ->getMock();

        $fakeResponse = json_encode([
            'data' => [
                ['title' => 'Título 1', 'user_name' => 'user1'],
                ['title' => 'Título 2', 'user_name' => 'user2'],
            ]
        ]);

        $repo->method('getApiResponse')->willReturn([$fakeResponse, 200]);

        $streams = $repo->getStreams();

        // Then
        $this->assertIsArray($streams);
        $this->assertCount(2, $streams);
        $this->assertInstanceOf(Stream::class, $streams[0]);
        $this->assertEquals('Título 1', $streams[0]->getTitle());
        $this->assertEquals('user1', $streams[0]->getUserName());
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturnsNull(): void
    {
        $credentials = [
            'client_id' => 'invalid-client-id',
            'access_token' => 'invalid-access-token',
        ];

        $repo = $this->getMockBuilder(TwitchAPIRepository::class)
            ->setConstructorArgs([$credentials])
            ->onlyMethods(['getApiResponse'])
            ->getMock();

        $repo->method('getApiResponse')->willReturn([null, 401]);

        $this->expectException(TwitchApiException::class);
        $this->expectExceptionMessage('Unauthorized. Twitch access token is invalid or has expired.');

        $result = $repo->getStreams();

        $this->assertNull($result);
    }
}
