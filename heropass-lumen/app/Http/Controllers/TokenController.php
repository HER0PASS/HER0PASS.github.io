<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;
use App\Services\GetApiKeyService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

class TokenController
{
    private TokenValidator $validator;
    private TokenService $service;

    public function __construct(
        TokenValidator $validator,
        TokenService $service,
    ) {
        $this->validator = $validator;
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function createToken(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->input('email'));
            $apiKey = $this->validator->validateApiKey($request->input('api_key'));

            return $this->service->createToken($email, $apiKey);
        } catch (EmptyEmailException | InvalidEmailAddressException | EmptyApiKeyException | InvalidApiKeyException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        }
    }
}
