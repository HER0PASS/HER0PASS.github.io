<?php

namespace Tests\Unit\Services;

use App\Services\GetUserByIdService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class GetUserByIdServiceTest extends TestCase
{
    private GetUserByIdService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repo = new FakeDataBaseRepository();
        $this->service = new GetUserByIdService($repo);
    }

    /**
     * @test
     */
    public function throwsExceptionIfUserDoesNotExist(): void
    {
        $response = $this->service->getUserData('99999');
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('User not found.', $data['error']);
    }

    /**
     * @test
     */
    public function getUserDaraIfUserExists()
    {
        $response = $this->service->getUserData('12345');
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Ninja', $data['display_name']);
    }
}
