<?php

namespace Feature\topsofthetops;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Laravel\Lumen\Testing\TestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetTopsofthetopsTest extends TestCase
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
    public function givenSinceMinorToTimestampReturnsDataBaseTops200()
    {
        $this->get('/analytics/topsofthetops?since=1000', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(200);
        $this->seeJson([
            'game_name' => 'Just Chatting',
        ]);
    }

    /**
     * @test
     */
    public function givenSinceGreaterToTimestampReturnsDataBaseTops200()
    {
        $this->get('/analytics/topsofthetops?since=1', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(200);
        $this->seeJson([
            'game_name' => 'Dota',
        ]);
    }

    /**
     * @test
     */
    public function givenNonNumericSinceReturns400()
    {
        $this->get('/analytics/topsofthetops?since=abcd', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(400);
        $this->seeJson([
            'error' => "Invalid 'since' parameter.",
        ]);
    }

    /**
     * @test
     */
    public function givenInvalidNumericSinceReturns400()
    {
        $this->get('/analytics/topsofthetops?since=-100', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(400);
        $this->seeJson([
            'error' => "Invalid 'since' parameter.",
        ]);
    }

    /**
     * @test
     */
    public function givenRequestWithInvalidTokenReturns401()
    {
        $this->get('/analytics/topsofthetops');
        $this->seeStatusCode(401);
    }
}
