<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function register(Request $request)
    {
        return 'Este es el endpoint de registro desde el controlador';
    }

    public function token(Request $request)
    {
        return 'Este es el endpoint de token desde el controlador';
    }

    public function topsofthetops(Request $request)
    {
        return 'Este es el endpoint de topsofthetops desde el controlador';
    }

    public function user(Request $request)
    {
        $id = $request->input('id');
        return 'Este es el endpoint de user para el id ' . $id . ' desde el controlador';
    }

    public function streams(Request $request)
    {
        return 'Este es el endpoint de streams desde el controlador';
    }

    public function streamsEnriched(Request $request)
    {
        $limit = $request->input('limit');
        return 'Este es el endpoint de streams enriquecidos para el limit ' . $limit . ' desde el controlador';
    }

}
