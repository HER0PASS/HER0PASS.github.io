<?php

namespace Tests\Http\Controllers;

use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\JsonResponse;
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
            'error' => 'API key is empty',
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
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Invalid API key format',
        ]);
    }
    public function testGetsTokenWhenGivenApiKeyAndEmail(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'testofuser2@example.com',
                'api_key' => '1ce0ec097bbbbb5b97b9e4e8ccfcba4l',
            ]
        );
        $response->assertJson([
            'token' => 'd28aab08263d18a5031bd0d5552444c9',
        ]);
    }
}
