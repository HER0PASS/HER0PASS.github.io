<?php

namespace Http\Middleware;

use App\Http\Middleware\TokenManager;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISession;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDataBaseRepository;

class TokenManagerIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeDataBaseRepository();
        $this->manager = new TokenManager($this->repository);
    }

    /**
     * @test
     */
    public function givenValidStoredTokenAndTokenIsActiveReturnsTrue(): void
    {
        $user_id = '123';
        $token = '6288f213b19339919569e8b43f1ad852';
        $expires_at = new \DateTime('+3 days');
        $session = new APISession($user_id, $token, $expires_at);
        $this->repository->storeSession($session);

        $this->assertTrue($this->manager->tokenIsActive($token));
    }

    /**
     * @test
     */
    public function givenNonExistentTokenReturnsFalse(): void
    {
        $token = 'non_existing_token_abcdef';

        $this->assertFalse($this->manager->tokenIsActive($token));
    }

    /**
     * @test
     */
    public function givenExpiredTokenInRepositoryReturnsFalse(): void
    {

        $expiredToken = 'expired_token';
        $expiredSession = new APISession('123', $expiredToken, new \DateTime('-2 hours'));
        $this->repository->storeSession($expiredSession);

        $manager = new TokenManager($this->repository);

        $this->assertFalse($manager->tokenIsActive($expiredToken));
    }
}
