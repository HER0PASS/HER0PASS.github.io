<?php

namespace Tests\Unit\Services;

use App\Services\GetEnrichedStreamsService;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeTwitchApiRepository;

class GetEnrichedStreamsServiceTest extends TestCase
{

    private GetEnrichedStreamsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $apiRepository = new FakeTwitchApiRepository();
        $this->service = new GetEnrichedStreamsService($apiRepository);
    }

    /**
     * @test
     */
    public function givenValidStreamsReturnsSuccessfulJsonResponse(): void
    {
        $limit = 2;
        $response = $this->service->getEnrichedStreamsData($limit);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertCount($limit, $data);
        $this->assertEquals('pokimane', $data[1]['user_name']);
    }
}
