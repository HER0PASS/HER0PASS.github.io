<?php

namespace Tests\Http\Controllers\GetUserById;

use App\Http\Controllers\GetUserById\GetUserByIdController;
use App\Http\Controllers\GetUserById\GetUserByIdValidator;
use App\Services\GetUserByIdService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GetUserByIdControllerTest extends TestCase
{
    protected $validator;
    protected $service;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks
        $this->validator = Mockery::mock(GetUserByIdValidator::class);
        $this->service = Mockery::mock(GetUserByIdService::class);

        // Crear instancia del controlador con mocks
        $this->controller = new GetUserByIdController($this->validator, $this->service);
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
    {
        // Crear request con token válido pero sin ID
        $request = Request::create('/analytics/user', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->service->shouldReceive('verificarToken')
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
    public function getsErrorIfIdParameterIsInvalid()
    {
        // Crear request con token válido pero ID inválido
        $request = Request::create('/analytics/user', 'GET', ['id' => '0']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->service->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('12345');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->with('0')
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

    /**
     * @test
     */
    public function getsUserDataSuccessfully()
    {
        // Crear request con token válido e ID válido
        $request = Request::create('/analytics/user', 'GET', ['id' => '12345']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->service->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('user-123');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->with('12345')
            ->andReturn(true);

        // Configurar mock para getUserData
        $userData = [
            'id' => '12345',
            'display_name' => 'TestUser',
            'profile_image_url' => 'http://example.com/image.jpg'
        ];

        $this->service->shouldReceive('getUserData')
            ->once()
            ->with('12345')
            ->andReturn(response()->json($userData, 200));

        // Ejecutar el método index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            json_encode($userData),
            $response->getContent()
        );
    }
}
