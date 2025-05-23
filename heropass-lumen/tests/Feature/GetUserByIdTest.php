<?php

namespace Tests\Feature;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetUserByIdTest extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->bind(DataBaseRepositoryInterface::class, FakeDataBaseRepository::class);
        $app->bind(TwitchApiRepositoryInterface::class, FakeTwitchApiRepository::class);

        return $app;
    }

    /**
     * @test
     */
    public function givenTwitchUserIdRegisteredInDBReturns200()
    {
        $this->get('/analytics/user?id=12345', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(200);
        $this->seeJson([
            'display_name' => 'Ninja',
        ]);
    }

    /**
     * @test
     */
    public function givenTwitchUserIdNonExistentReturns404()
    {
        $this->get('/analytics/user?id=99999', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(404);
        $this->seeJson([
            'error' => 'User not found.',
        ]);
    }

    /**
     * @test
     */
    public function givenRequestWithMissingTwitchUserIdReturns400()
    {
        $this->get('/analytics/user', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(400);
        $this->seeJson([
            'error' => 'Invalid or missing \'id\' parameter.',
        ]);
    }

    /**
     * @test
     */
    public function givenRequestWithInvalidTokenReturns401()
    {
        $this->get('/analytics/user?id=12345');
        $this->seeStatusCode(401);
    }
}
