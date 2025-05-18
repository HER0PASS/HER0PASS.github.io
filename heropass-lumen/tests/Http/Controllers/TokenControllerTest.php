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
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'heropass@gmail.com',
                'api_key' => '14932a25a74d001fd896c3cefdc860b8',
            ]
        );
        $response->assertJson([
            'token' => '2ed400fe932003eaeebf5e194f02eb05',
        ]);
    }
}
