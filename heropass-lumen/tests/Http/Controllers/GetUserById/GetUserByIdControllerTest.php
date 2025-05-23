<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdController;
use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use App\Services\GetUserByIdService;
use Illuminate\Http\Request;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class GetUserByIdControllerTest extends TestCase
{
    private GetUserByIdController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new GetUserByIdValidator();
        $service = new GetUserByIdService(new FakeDataBaseRepository());

        $this->controller = new GetUserByIdController($validator, $service);
    }

    /**
     * @test
     */
    public function gets400IfIdIsMissing(): void
    {
        $request = Request::create('/analytics/user', 'GET');

        $response = $this->controller->getUser($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Invalid or missing 'id' parameter.", $data['error']);
    }

    /**
     * @test
     */
    public function gets404IfUserIsNotFound(): void
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '99999']);

        $response = $this->controller->getUser($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User not found.", $data['error']);
    }


    /**
     * @test
     */
    public function getsUserDataIfIdIsValid()
    {
        $request = Request::create('/analytics/user', 'GET', ['id' => '12345']);

        $response = $this->controller->getUser($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('display_name', $data);
        $this->assertEquals('Ninja', $data['display_name']);
    }
}
