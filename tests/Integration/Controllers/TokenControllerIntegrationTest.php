<?php

namespace Integration\Controllers;

use App\Http\Controllers\Register\EmailValidator;
use App\Http\Controllers\Token\ApiKeyValidator;
use App\Http\Controllers\Token\TokenController;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class TokenControllerIntegrationTest extends TestCase
{
    private TokenController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $emailValidator = new EmailValidator();
        $apiKeyValidator = new ApiKeyValidator();
        $service = new TokenService(new FakeDataBaseRepository());

        $this->controller = new TokenController($emailValidator, $apiKeyValidator, $service);
    }

    /**
     * @test
     */
    public function givenMissingEmailReturns400WithCorrectErrorMessage(): void
    {
        $request = Request::create('/token', 'POST', ['api_key' => '6288f213b19339919569e8b43f1ad852']);
        $response = $this->controller->token($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayNotHasKey('token', $data);
        $this->assertEquals('The email is mandatory', $data['error']);
    }

    /**
     * @test
     */
    public function givenInvalidEmailReturns400WithCorrectErrorMessage(): void
    {
        $request = Request::create('/token', 'POST', ['email' => 'invalidEmail', 'api_key' => '6288f213b19339919569e8b43f1ad852']);
        $response = $this->controller->token($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayNotHasKey('token', $data);
        $this->assertEquals('The email must be a valid email address', $data['error']);
    }

    /**
     * @test
     */
    public function givenMissingApiKeyReturns400WithCorrectErrorMessage(): void
    {
        $request = Request::create('/token', 'POST', ['email' => 'user1@example.com']);
        $response = $this->controller->token($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayNotHasKey('token', $data);
        $this->assertEquals('The api_key is mandatory', $data['error']);
    }

    /**
     * @test
     */
    public function givenInvalidApiKeyReturns401WithCorrectErrorMessage(): void
    {
        $request = Request::create('/token', 'POST', ['email' => 'user1@example.com', 'api_key' => '__invalid|ApiKey__']);
        $response = $this->controller->token($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayNotHasKey('token', $data);
        $this->assertEquals('Unauthorized. API access token is invalid.', $data['error']);
    }

    /**
     * @test
     */
    public function givenValidEmailAndApiKeyReturns200WithCorrectData(): void
    {
        $request = Request::create('/token', 'POST', ['email' => 'user1@example.com', 'api_key' => '6288f213b19339919569e8b43f1ad852']);
        $response = $this->controller->token($request);
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $data);
    }
}
