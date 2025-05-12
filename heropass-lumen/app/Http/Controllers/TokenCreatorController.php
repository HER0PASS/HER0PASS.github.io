<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;
use App\Services\GetApiKeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenCreatorController
{
    private TokenCreatorValidator $validator;
    private GetApiKeyService $service;

    public function __construct(
        TokenCreatorValidator $validator,
        GetApiKeyService $service,
    ) {
        $this->validator = $validator;
        $this->service = $service;
    }
    public function createToken(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->input('email'));
            $apiKey = $this->validator->validateApiKey($request->input('api_key'));

            return new JsonResponse(
                [
                    'email' => $email,
                    'api_key' => $apiKey],
                200
            );/*Aqui vamos a implementar en la siguiente integracion la llamada al service*/
        } catch (EmptyEmailException | InvalidEmailAddressException | EmptyApiKeyException | InvalidApiKeyException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        }
    }
}
