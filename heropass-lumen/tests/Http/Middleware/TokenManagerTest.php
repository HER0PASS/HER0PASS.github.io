<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\TokenManager;
use Tests\TestCase;

class TokenManagerTest extends TestCase
{
    /** Test */
    public function testGets200AndTokenWhenValidApiKeyAndEmailAreGiven(): void
    {
        $response = $this->call(
            'POST',
            '/token',
            [
                'email' => 'notSanitazed@mail.com',
                'api_key' => 'a39f4e4a9fd2329b9d190e18e67e58c7'
            ]
        );
        $response->assertJson([
            'userId' => '12',
        ]);
    }
}
