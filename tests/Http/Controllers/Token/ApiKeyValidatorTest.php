<?php

namespace Tests\Http\Controllers\Token;

use App\Http\Controllers\Token\ApiKeyValidator;
use PHPUnit\Framework\TestCase;

class ApiKeyValidatorTest extends TestCase
{
    private ApiKeyValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ApiKeyValidator();
    }

    /**
     * @test
     */
    public function givenValidApiKeyReturnsApiKey(): void
    {
        $validKey = str_repeat('A', 32);
        $this->assertSame($validKey, $this->validator->validateApiKey($validKey));
    }

    /**
     * @test
     */
    public function givenEmptyApiKeyReturnsEmptyApiKeyException(): void
    {
        $this->expectException(\App\Exceptions\EmptyApiKeyException::class);
        $this->expectExceptionMessage("The api_key is mandatory");

        $this->validator->validateApiKey(null);
    }

    /**
     * @test
     */
    public function givenShortInvalidApiKeyReturnsInvalidApiKeyException(): void
    {
        $this->expectException(\App\Exceptions\InvalidApiKeyException::class);
        $this->expectExceptionMessage("Unauthorized. API access token is invalid.");

        $this->validator->validateApiKey('shortkey123');
    }

    /**
     * @test
     */
    public function givenInvalidCharactersInApiKeyReturnsInvalidApiKeyException(): void
    {
        $this->expectException(\App\Exceptions\InvalidApiKeyException::class);
        $this->expectExceptionMessage("Unauthorized. API access token is invalid.");

        $this->validator->validateApiKey('invalid_key_with_çhar$%#');
    }
}
