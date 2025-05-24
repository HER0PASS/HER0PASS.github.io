<?php

namespace App\Http\Controllers\Token;

use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidApiKeyException;
use App\Exceptions\InvalidEmailAddressException;
use App\Http\Controllers\Register\EmailValidator;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController
{
    private EmailValidator $emailValidator;
    private ApiKeyValidator $apiKeyValidator;
    private TokenService $service;

    public function __construct(
        EmailValidator $validator,
        ApiKeyValidator $apiKeyValidator,
        TokenService $service,
    ) {
        $this->emailValidator = $validator;
        $this->apiKeyValidator = $apiKeyValidator;
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function token(Request $request): JsonResponse
    {
        try {
            $email = $this->emailValidator->validateEmail($request->input('email'));
            $apiKey = $this->apiKeyValidator->validateApiKey($request->input('api_key'));

            $session = $this->service->createToken($email, $apiKey);
            if (!$session) {
                return response()->json([
                    'error' => 'Unauthorized. API access token is invalid.'
                ], 401);
            }

            return response()->json([
                'token' => $session->getToken()
            ], 200);
        } catch (EmptyEmailException | EmptyApiKeyException | InvalidEmailAddressException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        } catch (InvalidApiKeyException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 401);
        }
    }
}
