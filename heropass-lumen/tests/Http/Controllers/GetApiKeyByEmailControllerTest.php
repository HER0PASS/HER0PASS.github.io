<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\GetApiKeyByEmailController;
use Illuminate\Http\JsonResponse;
use App\Exceptions\InvalidEmailAddressException;

use Tests\TestCase;

class GetApiKeyByEmailControllerTest extends TestCase
{

    /**
     * @test
     */
    public function testGivenRequestWithoutEmailReturns400(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            []);
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Invalid parameter, email is required',
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
            ]);
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'The email given must be a valid address',
        ]);
    }
    /**
     * @test
     */
    public function testGivenRequestWithNotSanitazedEmailReturnsSanitazedEmail(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            [
                'email' => '(notSanitazed@mail.com)',
            ]);
        $response->assertStatus(200);
        $response->assertJson([
            'email' => 'notSanitazed@mail.com',
        ]);
    }
}
