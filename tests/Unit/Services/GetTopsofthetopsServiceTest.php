<?php

namespace Tests\Unit\Services;

use App\Services\GetTopsofthetopsService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetTopsofthetopsServiceTest extends TestCase
{
    private GetTopsofthetopsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repo = new FakeDataBaseRepository();
        $api = new FakeTwitchApiRepository();

        $this->service = new GetTopsofthetopsService($repo, $api);
    }


    /**
     * @test
     */
    public function givenValidSinceReturnsData()
    {
        $response = $this->service->getTopsofthetopsData(60);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        $this->assertEquals('Just Chatting', $data[0]['game_name']);
        $this->assertEquals('League of Legends', $data[1]['game_name']);
        $this->assertEquals('Fortnite', $data[2]['game_name']);
    }
}
