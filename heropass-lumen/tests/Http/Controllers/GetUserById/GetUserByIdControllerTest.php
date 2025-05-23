<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdController;
use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use App\Services\GetUserByIdService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GetUserByIdControllerTest extends TestCase
{
    private GetUserByIdValidator $validator;
    private GetUserByIdController $controller;
    private GetUserByIdService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = new GetUserByIdValidator();
        $service = new GetUserByIdService();
        $this->controller = new GetUserByIdController($validator, $service);
    }

    /**
     * @test
     */
    public function gets400IfIdIsMissing(): void
    {
        $request = new Request();

        $response = $this->controller->getUser($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => "Invalid or missing 'id' parameter."
        ], $responseData);
    }

    /**
     * @test
     */
    public function gets404IfUserIsNotFound(): void
    {
        $request = new Request([], ['id' => 'nonexistent-id']);

        $response = $this->controller->getUser($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'User not found'
        ], $responseData);
    }


    /**
     * @test
     */
    public function getsUserDataIfIdIsValid()
    {
        $request = new Request([], ['id' => '12345']);

        $response = $this->controller->getUser($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('display_name', $responseData);
    }
}
