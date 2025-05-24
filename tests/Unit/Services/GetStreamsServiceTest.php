<?php

namespace Tests\Unit\Services;

use App\Services\GetStreamsService;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetStreamsServiceTest extends TestCase
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
    public function givenValidStreamsReturnsSuccessfulJsonResponse(): void
    {
        $response = $this->service->getStreamsData();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertCount(3, $data);
        $this->assertEquals('messi', $data[0]['user_name']);
    }
}
