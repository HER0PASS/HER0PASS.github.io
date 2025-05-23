<?php

namespace App\Http\Controllers\Register;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Models\APIUser;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class RegisterController extends BaseController
{
    private EmailValidator $validator;
    private RegisterService $service;

    public function __construct(EmailValidator $validator, RegisterService $service)
    {
        $this->validator = $validator;
        $this->service = $service;
    }
    public function register(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->input('email'));

            $user = new APIUser(null, $email);

            $registeredUser = $this->service->registerUser($user);

            return response()->json(['api_key' => $registeredUser->getApiKey()], 200);
        } catch (EmptyEmailException | InvalidEmailAddressException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        }
    }
}
