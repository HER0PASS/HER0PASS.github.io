<?php

namespace Tests\Http\Controllers;

use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TokenValidator;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testGets400WhenEmailIsMissing(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'api_key' => 'validApiKey',
            ]
        );
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email is mandatory',
        ]);
    }

    /** Test */
    public function testGets400WhenNotValidEmailIsGiven(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'invalidEmail'
            ]
        );
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email must be a valid email address',
        ]);
    }

    /** Test */
    public function testGets400WhenNoApiKeyIsGiven(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'test@example.com'
            ]
        );
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The api_key is mandatory',
        ]);
    }
    /** Test */
    public function testGets400WhenApiKeyIsGiveIsNotValid(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'test@example.com',
                'api_key' => '__invalid|ApiKey__'
            ]
        );
        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized. API access token is invalid.',
        ]);
    }
    public function testGetsTokenWhenGivenApiKeyAndEmail(): void
    {
        $token = 'mocked-token';
        $mockResponse = new JsonResponse(['token' => $token], 200);

        $mockService = Mockery::mock(TokenService::class);
        $mockService->shouldReceive('createToken')
            ->once()
            ->with('test@example.com', 'validApiKey')
            ->andReturn($mockResponse);

        $mockValidator = Mockery::mock(TokenValidator::class);
        $mockValidator->shouldReceive('validateEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn('test@example.com');

        $mockValidator->shouldReceive('validateApiKey')
            ->once()
            ->with('validApiKey')
            ->andReturn('validApiKey');

        $this->app->instance(TokenService::class, $mockService);
        $this->app->instance(TokenValidator::class, $mockValidator);

        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'test@example.com',
                'api_key' => 'validApiKey',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['token' => $token]);
    }
}
