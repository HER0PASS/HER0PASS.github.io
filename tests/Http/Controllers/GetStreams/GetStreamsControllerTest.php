<?php

namespace Tests\Http\Controllers\GetStreams;

use App\Http\Controllers\GetStreams\GetStreamsController;
use App\Http\Controllers\GetStreams\GetStreamsValidator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GetStreamsControllerTest extends TestCase
{
    protected $validator;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();        // Crear mock para el validador
        $this->validator = Mockery::mock(GetStreamsValidator::class);

        // Crear mock parcial para el controlador
        $this->controller = Mockery::mock(GetStreamsController::class, [$this->validator])
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
    public function getsErrorIfAuthorizationHeaderIsMissing()
    {
        // Crear request sin Authorization header
        $request = Request::create('/analytics/streams', 'GET');

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
    public function getsErrorIfAuthorizationTokenIsInvalid()
    {
        // Crear request con token inválido
        $request = Request::create('/analytics/streams', 'GET');
        $request->headers->set('Authorization', 'Bearer invalid-token');

        // Configurar mock para verificarToken
        $this->controller->shouldReceive('verificarToken')
            ->once()
            ->with('invalid-token')
            ->andReturn(false);

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
    public function returnsStreamsDataWhenRequestIsValid()
    {
        // Crear request con token válido
        $request = Request::create('/analytics/streams', 'GET');
        $request->headers->set('Authorization', 'Bearer valid-token');

        // Configurar mock para verificarToken
        $this->controller->shouldReceive('verificarToken')
            ->once()
            ->with('valid-token')
            ->andReturn('12345');

        // Configurar mock para el validador
        $this->validator->shouldReceive('validate')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        // Configurar mock para obtenerToken
        $this->controller->shouldReceive('obtenerToken')
            ->once()
            ->withNoArgs()
            ->andReturn([
                'client_id' => 'test-client-id',
                'access_token' => 'test-access-token'
            ]);

        // Configurar mock para getStreamsDataFromApi
        $this->controller->shouldReceive('getStreamsDataFromApi')
            ->once()
            ->with([
                'client_id' => 'test-client-id',
                'access_token' => 'test-access-token'
            ])
            ->andReturn([
                ['title' => 'Stream 1', 'user_name' => 'User 1'],
                ['title' => 'Stream 2', 'user_name' => 'User 2']
            ]);

        // Ejecutar el método index con el request
        $response = $this->controller->index($request);

        // Verificar el resultado
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            '[{"title":"Stream 1","user_name":"User 1"},{"title":"Stream 2","user_name":"User 2"}]',
            $response->getContent()
        );
    }
}
