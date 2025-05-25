<?php

namespace Tests\Feature\EnrichedStreams;

use App\Http\Middleware\TokenManager;
use App\Interfaces\TwitchApiRepositoryInterface;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Tests\Fakes\FakeTwitchApiRepository;

class GetEnrichedStreamsEndpointTest extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../bootstrap/app.php';

        $app->bind(TwitchApiRepositoryInterface::class, FakeTwitchApiRepository::class);

        return $app;
    }

    /**
     * @test
     */
    public function givenValidTokenReturns200AndEnrichedStreams(): void
    {
        $mockTokenManager = \Mockery::mock(TokenManager::class);
        $mockTokenManager->shouldReceive('tokenIsActive')
            ->with('valid_token')
            ->andReturn(true);

        $this->app->instance(TokenManager::class, $mockTokenManager);

        $this->get('/analytics/streams/enriched?limit=3', [
            'Authorization' => 'Bearer valid_token'
        ]);

        $this->seeStatusCode(200);

        $this->seeJson([
            'title' => 'Epic Gaming Session',
            'user_name' => 'ninja',
            "profile_image_url" => "https://example.com/ninja.jpg"
        ]);

        $this->seeJson([
            'title' => 'Let’s Go!',
            'user_name' => 'pokimane',
            "user_display_name" => "Pokimane"
        ]);

        $this->seeJson([
            'title' => 'Playing with viewers',
            'user_name' => 'shroud',
            "viewer_count" => 15000
        ]);
    }

    /**
     * @test
     */
    public function givenInvalidTokenReturns401(): void
    {
        $mockTokenManager = \Mockery::mock(TokenManager::class);
        $mockTokenManager->shouldReceive('tokenIsActive')
            ->with('invalid_token')
            ->andReturn(false);

        $this->app->instance(TokenManager::class, $mockTokenManager);

        $this->get('/analytics/streams/enriched?limit=3', [
            'Authorization' => 'Bearer invalid_token'
        ]);

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
        $this->get('/analytics/streams/enriched?limit=3');
        $this->seeStatusCode(401);
        $this->seeJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }
}
