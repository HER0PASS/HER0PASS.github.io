<?php

namespace Tests\Http\Controllers;

use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    /**
     * @test
     */
    public function testGivenRequestWithoutEmailReturns400(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            []
        );
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email is mandatory',
        ]);
    }
    /**
     * @test
     */
    public function testGivenRequestWithNotValidEmailReturns400AndError(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            [
                'email' => 'notValidMail.com',
            ]
        );
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email must be a valid email address',
        ]);
    }
    /**
     * @test
     */
    public function testGivenRequestWithNotSanitazedEmailReturnsApiKey(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            [
                'email' => '(jejeje@mail.com)',
            ]
        );
        $response->assertJsonStructure([
            'api_key',
        ]);
    }
}
