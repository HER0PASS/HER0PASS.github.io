<?php

namespace Register\Integration;

use App\Http\Controllers\Register\EmailValidator;
use App\Http\Controllers\Register\RegisterController;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class RegisterControllerTest extends TestCase
{
    private RegisterController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new EmailValidator();
        $service = new RegisterService(new FakeDataBaseRepository());

        $this->controller = new RegisterController($validator, $service);
    }

    /**
     * @test
     */
    public function givenRequestWithValidEmailReturnsAPIKeyAnd200Code(): void
    {
        $request = Request::create('/register', 'POST', ['email' => 'user1@example.com']);

        $response = $this->controller->register($request);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('api_key', $data);
        $this->assertNotEmpty($data['api_key']);
    }

    /**
     * @test
     */
    public function givenRequestWithNotSanitazedEmailReturnsApiKey(): void
    {
        $request = Request::create('/register', 'POST', ['email' => '(user1@example.com)']);

        $response = $this->controller->register($request);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('api_key', $data);
        $this->assertNotEmpty($data['api_key']);
    }


    /**
     * @test
     */
    public function givenRequestWithoutEmailReturns400AndErrorMessage(): void
    {
        $request = Request::create('/register', 'POST');

        $response = $this->controller->register($request);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('The email is mandatory', $data['error']);
    }

    /**
     * @test
     */
    public function givenRequestWithInvalidEmailReturns400AndErrorMessage(): void
    {
        $request = Request::create('/register', 'POST', ['email' => 'user1@example']);

        $response = $this->controller->register($request);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('The email must be a valid email address', $data['error']);
    }
}
