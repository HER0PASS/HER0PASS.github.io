<?php

namespace App\Http\Controllers;

use App\Exceptions\EmptyEmailException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Js;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class GetApiKeyByEmailController extends BaseController
{
    private GetApiKeyByEmailValidator $validator;

    public function __construct(
        GetApiKeyByEmailValidator $validator,
    ){
        $this->validator = $validator;
    }
    public function getApiKeyData(Request $request)
    {
        try {
            $email = $this->validator->validate($request->input('email'));
            return $email;
        } catch (EmptyEmailException) {
            return new JsonResponse([
                'error' => 'Missing Email',
            ], 400);
        }
    }
}
