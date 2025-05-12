<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Services\GetApiKeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Js;
use Laravel\Lumen\Routing\Controller as BaseController;
use PharIo\Manifest\InvalidEmailException;

class GetApiKeyByEmailController extends BaseController
{
    private GetApiKeyByEmailValidator $validator;
    private GetApiKeyService $service;

    public function __construct(
        GetApiKeyByEmailValidator $validator,
        GetApiKeyService $service,
    ) {
        $this->validator = $validator;
        $this->service = $service;
    }
    public function getApiKeyData(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validate($request->input('email'));

            return new JsonResponse(['email' => $email], 200);/*Ahora hariamos el service*/
        } catch (EmptyEmailException | InvalidEmailAddressException $e) {
            return new JsonResponse([
                'error' =>  $e->getMessage(),
            ], 400);
        }
    }
}
