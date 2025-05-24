<?php

namespace Feature;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APIUser;
use Laravel\Lumen\Testing\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class RegisterEndpointTest extends TestCase
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
    public function givenValidParametersReturnsAPIKey(): void
    {
        $this->post('/register', ['email' => 'e2e@example.com']);

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['api_key']);
    }

    /**
     * @test
     */
    public function givenNoEmailReturns400AndErrorMessage(): void
    {
        $this->post('/register');

        $this->seeStatusCode(400);

        $this->seeStatusCode(400);
        $this->seeJson(['error' => 'The email is mandatory']);
    }

    /**
     * @test
     */
    public function givenInvalidEmailReturns400AndErrorMessage(): void
    {
        $this->post('/register', ['email' => 'user1@example']);

        $this->seeStatusCode(400);
        $this->seeJson(['error' => 'The email must be a valid email address']);
    }


    /**
     * @test
     */
    public function givenRegisteredUserReturnsUpdatedAPIKey(): void
    {

        // Registrar un usuario
        $this->app->bind(DataBaseRepositoryInterface::class, function () {
            $repo = new FakeDataBaseRepository();
            $repo->storeUser(new APIUser(1, 'user1@example.com', 'original_key'));
            return $repo;
        });

        // Hacer una peticion con el mismo usuario
        $response = $this->post('/register', [
            'email' => 'user1@example.com'
        ]);

        // Validar que el formato de la respuesta es correcta
        $response->seeStatusCode(200);
        $response->seeJsonStructure(['api_key']);

        // Validar que se ha generado una nueva api key
        $newApiKey = json_decode($response->response->getContent(), true)['api_key'];
        $this->assertNotEquals('original_key', $newApiKey);
    }
}
