<?php

namespace App\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class RegisterController extends BaseController
{
    private RegisterValidator $validator;
    private RegisterService $service;

    public function __construct(RegisterValidator $validator, RegisterService $service) {
        $this->validator = $validator;
        $this->service = $service;
    }
    public function getApiKeyData(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validate($request->input('email'));
            return $this->service->registerUser($email);
        } catch (EmptyEmailException | InvalidEmailAddressException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        }
    }
}
