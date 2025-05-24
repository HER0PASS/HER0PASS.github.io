<?php

namespace Http\Controllers\Register;

use App\Http\Controllers\Register\EmailValidator;
use App\Http\Controllers\Register\RegisterController;
use App\Models\APIUser;
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

    /**
     * @test
     */
    public function givenRequestWithRegisteredUserReturnsUpdatedAPIKey(): void
    {
        $oldKey = '6288f213b19339919569e8b43f1ad852';
        $existingUser = new APIUser(1, 'user1@example.com', $oldKey);

        $repository = new FakeDataBaseRepository();
        $repository->storeUser($existingUser);

        $service = new RegisterService($repository);
        $validator = new EmailValidator();
        $controller = new RegisterController($validator, $service);

        $request = Request::create('/register', 'POST', ['email' => 'user1@example.com']);
        $response = $controller->register($request);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('api_key', $data);
        $this->assertNotEquals($oldKey, $data['api_key']);

        $storedUser = $repository->getAPIUserByEmail($existingUser);
        $this->assertEquals($data['api_key'], $storedUser->getApiKey());
    }
}
