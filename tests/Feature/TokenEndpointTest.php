<?php

namespace Tests\Feature;

use App\Interfaces\DataBaseRepositoryInterface;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class TokenEndpointTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';

        $app->bind(DataBaseRepositoryInterface::class, FakeDataBaseRepository::class);

        return $app;
    }

    /**
     * @test
     */
    public function givenValidCredentialsReturnsToken(): void
    {
        $this->post('/token', [
            'email' => 'user1@example.com',
            'api_key' => '6288f213b19339919569e8b43f1ad852'
        ]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['token']);
    }

    /**
     * @test
     */
    public function givenMissingEmailReturns400(): void
    {
        $this->post('/token', [
            'api_key' => '6288f213b19339919569e8b43f1ad852'
        ]);

        $this->seeStatusCode(400);
        $this->seeJson([
            'error' => 'The email is mandatory'
        ]);
    }

    /**
     * @test
     */
    public function givenInvalidEmailReturns400(): void
    {
        $this->post('/token', [
            'email' => 'invalidEmail',
            'api_key' => '6288f213b19339919569e8b43f1ad852'
        ]);

        $this->seeStatusCode(400);
        $this->seeJson(['error' => 'The email must be a valid email address']);
    }

    /**
     * @test
     */
    public function givenMissingApiKeyReturns400(): void
    {
        $this->post('/token', [
            'email' => 'user1@example.com',
        ]);

        $this->seeStatusCode(400);
        $this->seeJson(['error' => 'The api_key is mandatory']);
    }

    /**
     * @test
     */
    public function givenInvalidApiKeyReturns401(): void
    {
        $this->post('/token', [
            'email' => 'user1@example.com',
            'api_key' => 'invalid_!@#api_key'
        ]);

        $this->seeStatusCode(401);
        $this->seeJson(['error' => 'Unauthorized. API access token is invalid.']);
    }
}
