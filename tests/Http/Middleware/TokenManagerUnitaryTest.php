<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\TokenManager;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISession;
use App\Models\APIUser;
use App\Services\TokenService;
use Mockery;
use Tests\Fakes\FakeDataBaseRepository;
use Tests\TestCase;

class TokenManagerUnitaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(DataBaseRepositoryInterface::class);
        date_default_timezone_set('Europe/Madrid');
    }

    /**
     * @test
     */
    public function givenActiveTokenReturnsTrue()
    {
        $token = '6288f213b19339919569e8b43f1ad852';
        $session = new APISession(1, $token, new \DateTime('+1 day'));

        $this->repository
            ->shouldReceive('getSessionByToken')
            ->once()
            ->with($token)
            ->andReturn($session);

        $manager = new TokenManager($this->repository);

        $this->assertTrue($manager->tokenIsActive($token));
    }


    /**
     * @test
     */
    public function givenExpiredTokenReturnsFalse(): void
    {
        $token = '6288f213b19339919569e8b43f1ad852';
        $session = new APISession(1, $token, new \DateTime('-1 hour'));

        $this->repository
            ->shouldReceive('getSessionByToken')
            ->once()
            ->with($token)
            ->andReturn($session);

        $manager = new TokenManager($this->repository);

        $this->assertFalse($manager->tokenIsActive($token));
    }
}
