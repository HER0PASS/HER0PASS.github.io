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
    private GetUserByIdValidator $validator;
    private GetUserByIdController $controller;
    private GetUserByIdService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mock para el validador
        $this->validator = Mockery::mock(GetUserByIdValidator::class);
        $this->service = Mockery::mock(GetUserByIdService::class);
        $this->controller = Mockery::mock(GetUserByIdController::class, [$this->validator, $this->service])
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
    {
        // Crear request con token válido pero sin ID
        $request = Request::create('/analytics/user', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para el validador
        $this->validator
            ->shouldReceive('validateRequest')
            ->with($request)
            ->andReturn([
                'isValid' => false,
                'error' => "Invalid or missing 'id' parameter.",
                'status' => 400
            ]);

        // Ejecutar el metodo getUser con el request
        $response = $this->controller->getUser($request);

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

        // Configurar mock para el validador
        $this->validator
            ->shouldReceive('validateRequest')
            ->with($request)
            ->andReturn([
                'isValid' => false,
                'error' => "Invalid or missing 'id' parameter.",
                'status' => 400
            ]);

        // Ejecutar el metodo getUser con el request
        $response = $this->controller->getUser($request);

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
    public function getsErrorIfIdParameterIsNotNumeric()
    {
        // Crear request con token válido pero ID no numérico
        $request = Request::create('/analytics/user', 'GET', ['id' => 'abc']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para el validador
        $this->validator
            ->shouldReceive('validateRequest')
            ->with($request)
            ->andReturn([
                'isValid' => false,
                'error' => "Invalid or missing 'id' parameter.",
                'status' => 400
            ]);

        // Ejecutar el metodo getUser con el request
        $response = $this->controller->getUser($request);

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

        // Configurar mock para el validador
        $this->validator
            ->shouldReceive('validateRequest')
            ->with($request)
            ->andReturn([
                'isValid' => false,
                'error' => "Unauthorized. Twitch access token is invalid or has expired.",
                'status' => 401
            ]);

        // Ejecutar el metodo getUser con el request
        $response = $this->controller->getUser($request);

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
    public function getsErrorIfTokenIsInvalid()
    {
        $response = $this->call('GET', '/analytics/user', ['id' => '123'], [
            'Authorization' => 'Bearer invalid-token',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ]);
    }

    /**
     * @test
     */
    public function getsUserDataSuccessfully()
    {
        // Crear request con token válido e ID válido
        $request = Request::create('/analytics/user', 'GET', ['id' => '12345']);
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para validateRequest
        $this->validator
            ->shouldReceive('validateRequest')
            ->with($request)
            ->andReturn([
                'isValid' => true,
                'token' => 'valid-token',
                'id' => '12345'
            ]);

        // Configurar mock para verificarToken
        $this->validator
            ->shouldReceive('verificarToken')
            ->with('valid-token')
            ->andReturn('user-123');

        // sevicio devuelve lo esperado
        $expectedResponse = response()->json([
            'id' => '12345',
            'display_name' => 'TestUser',
            'profile_image_url' => 'http://example.com/image.jpg'
        ]);
        $this->service
            ->shouldReceive('getUserData')
            ->with('12345')
            ->andReturn($expectedResponse);

        // Ejecutar el metodo getUser con el request
        $response = $this->controller->getUser($request);

        // Verificar el resultado
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            $expectedResponse->getContent(),
            $response->getContent()
        );
    }
}
