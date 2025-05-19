<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;

class Authenticate
{
    private TokenManager $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse([
                'error' => 'Unauthorized. Token is invalid or expired.',
            ], 401);
        }

        $token = substr($authHeader, 7); // Eliminar "Bearer "

        if (!$this->tokenManager->tokenIsActive($token)) {
            return new JsonResponse([
                'error' => 'Unauthorized. Token is invalid or expired.',
            ], 401);
        }

        return $next($request);
    }
}
