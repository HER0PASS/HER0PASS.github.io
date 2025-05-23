<?php

namespace Feature;

use App\Interfaces\DataBaseRepositoryInterface;
use Laravel\Lumen\Testing\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class RegisterEndpointTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../bootstrap/app.php';

        $app->bind(DataBaseRepositoryInterface::class, FakeDataBaseRepository::class);

        return $app;
    }

    /**
     * @test
     */
    public function givenValidRequestReturnsAPIKey(): void
    {
        $this->post('/register', ['email' => 'e2e@example.com']);

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['api_key']);
    }
}
