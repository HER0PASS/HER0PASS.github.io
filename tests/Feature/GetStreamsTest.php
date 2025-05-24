<?php

namespace Tests\Feature;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\Fakes\FakeTwitchApiRepository;

class GetStreamsTest extends BaseTestCase
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
    public function givenValidTokenReturnsStreamsData(): void
    {
        $this->get('/analytics/streams', ['Authorization' => 'Bearer valid_token']);
        $this->seeStatusCode(200);

        $this->seeJson([
            'title' => 'Analizando la nueva plantilla del FC Barcelona',
            'user_name' => 'messi'
        ]);

        $this->seeJson([
            'title' => 'Jugando al Fortnite con amigos',
            'user_name' => 'miketroke'
        ]);

        $this->seeJson([
            'title' => 'Las aventuras del peiro',
            'user_name' => 'dembele'
        ]);
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401(): void
    {
        $this->get('/analytics/streams', ['Authorization' => 'Bearer invalid_token']);
        $this->seeStatusCode(401);
        $this->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }

    /**
     * @test
     */
    public function givenNoTokenReturns401(): void
    {
        $this->get('/analytics/streams');
        $this->seeStatusCode(401);
        $this->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
}
