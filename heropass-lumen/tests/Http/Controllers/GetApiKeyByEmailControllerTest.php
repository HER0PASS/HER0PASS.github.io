<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\GetApiKeyByEmailController;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class GetApiKeyByEmailControllerTest extends TestCase
{

    /** Test sin email */
    public function testGivenRequestWithoutEmailReturns400(): void
    {
        $response = $this->call(
            'POST',
            '/register',
            []);
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Missing Email',
        ]);
    }
}
