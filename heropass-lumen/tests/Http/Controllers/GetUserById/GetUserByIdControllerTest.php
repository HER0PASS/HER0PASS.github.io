<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdController;
use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GetUserByIdControllerTest extends TestCase
{
    protected $validator;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mock para el validador
        $this->validator = Mockery::mock(GetUserByIdValidator::class);

        // Crear mock parcial para el controlador
        $this->controller = Mockery::mock(GetUserByIdController::class, [$this->validator])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getsErrorIfIdParameterIsMissing()
    {        // Crear request con token válido pero sin ID
        $request = Request::create('/analytics/user', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->controller->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('12345');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->with(null)
            ->andReturn(false);

        // Ejecutar el método index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Invalid or missing \'id\' parameter."}',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function getsErrorIfIdParameterIsLessThanOne()
    {
        // Crear request con token válido pero ID menor que 1
        $request = Request::create('/analytics/user', 'GET', ['id' => '0']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->controller->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('12345');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->with('0')
            ->andReturn(false);

        // Ejecutar el metodo index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Invalid or missing \'id\' parameter."}',
            $response->getContent()
        );
    }    /**
     * @test
     */
    public function getsErrorIfIdParameterIsNotNumeric()
    {
        // Crear request con token válido pero ID no numérico
        $request = Request::create('/analytics/user', 'GET', ['id' => 'abc']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->controller->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('12345');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->with('abc')
            ->andReturn(false);

        // Ejecutar el método index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Invalid or missing \'id\' parameter."}',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function getsErrorIfAuthorizationHeaderIsMissing()
    {
        // Crear request sin Authorization header
        $request = Request::create('/analytics/user', 'GET', ['id' => '1']);

        // Ejecutar el método index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(
            '{"error":"Unauthorized. Twitch access token is invalid or has expired."}',
            $response->getContent()
        );
    }
}
