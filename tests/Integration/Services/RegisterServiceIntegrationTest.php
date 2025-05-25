<?php

namespace Integration\Services;

use App\Models\APIUser;
use App\Services\RegisterService;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class RegisterServiceIntegrationTest extends TestCase
{
    private RegisterService $service;
    private FakeDataBaseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeDataBaseRepository();
        $this->service = new RegisterService($this->repository);
    }

    /**
     * @test
     */
    public function givenNewUserReturnsAPIKey(): void
    {

        $user = new APIUser(null, 'user1@example.com');
        $registeredUser = $this->service->registerUser($user);

        $this->assertNotEmpty($registeredUser->getApiKey());
        $this->assertEquals('user1@example.com', $registeredUser->getEmail());
    }

    /**
     * @test
     */
    public function givenRegisteredUserReturnsNewAPIKey(): void
    {
        $oldKey = '6288f213b19339919569e8b43f1ad852';

        $user = new APIUser(1, 'user1@example.com', $oldKey);

        $registeredUser = $this->service->registerUser($user);

        $this->assertNotEquals($oldKey, $registeredUser->getApiKey());
        $this->assertEquals('user1@example.com', $registeredUser->getEmail());
    }
}
