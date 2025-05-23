<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use App\Models\TwitchUser;
use Illuminate\Support\Facades\DB;

class DataBaseRepository implements DataBaseRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser
    {
        $row = DB::table('TwitchUsers')
            ->where('idUser', $userId)
            ->first();

        if (!$row || !$row->data) {
            return null;
        }

        $userData = json_decode($row->data, true);
        return TwitchUser::fromArray($userData);
    }

    public function saveTwitchUser(TwitchUser $user): void
    {
        DB::table('TwitchUsers')->updateOrInsert(
            ['idUser' => $user->getId()],
            ['data' => json_encode($user->toArray())]
        );
    }

    public function getAPIUserByEmail($email): ?APIUser
    {
        $row = DB::table('users')
            ->where('email', $email)
            ->first();

        if (!$row) {
            return null;
        }

        return new APIUser($row->id, $row->email, $row->api_key);
    }

    public function checkAPIUserExistence(APIUser $apiUser): ?APIUser
    {
        $row = DB::table('users')
            ->where('email', $apiUser->getEmail())
            ->where('api_key', $apiUser->getApiKey())
            ->first();

        return $row ? $apiUser : null;
    }

    public function updateAPIUserAPIKey(APIUser $apiUser): void
    {
        DB::table('users')
            ->where('email', $apiUser->getEmail())
            ->update(['api_key' => $apiUser->getApiKey()]);
    }

    public function registerAPIUser(APIUser $apiUser): void
    {
        DB::table('users')->insert([
            'email' => $apiUser->getEmail(),
            'api_key' => $apiUser->getApiKey(),
        ]);
    }

    public function getSessionByToken($token): ?APISessions
    {
        $row = DB::table('sessions')
            ->where('token', $token)
            ->first();

        if (!$row) {
            return null;
        }

        return new APISessions(
            (int) $row->user_id,
            $row->token,
            new \DateTime($row->expires_at)
        );
    }

    public function getSessionByUserId($user_id): ?APISessions
    {
        $row = DB::table('sessions')
            ->where('user_id', $user_id)
            ->first();

        if (!$row) {
            return null;
        }

        return new APISessions(
            (int) $row->user_id,
            $row->token,
            new \DateTime($row->expires_at)
        );
    }

    public function registerSession(APISessions $apiSession): void
    {
        DB::table('sessions')->insert([
            'user_id' => $apiSession->getUserId(),
            'token' => $apiSession->getToken(),
            'expires_at' => $apiSession->getExpiresAt()
        ]);
    }

    public function updateSession(APISessions $apiSession): void
    {
        DB::table('sessions')
            ->where('user_id', $apiSession->getUserId())
            ->update(['token' => $apiSession->getToken(), 'expires_at' => $apiSession->getExpiresAt()]);
    }
}
