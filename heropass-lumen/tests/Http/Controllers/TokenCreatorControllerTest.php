<?php

namespace Tests\Http\Controllers;

use Tests\TestCase;

class TokenCreatorControllerTest extends TestCase
{
    /** Test */
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
            'error' => 'Invalid parameter, email is required',
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
            'error' => 'The email given must be a valid address',
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

    /** Test */
    public function testGets200AndReturnJsonFormatWhenApiKeyAndEmailGivenIsValid(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'test@example.com',
                'api_key' => 'correctApiKeyFormat'
            ]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'email' => 'test@example.com',
            'api_key' => 'correctApiKeyFormat'
        ]);
    }
}
