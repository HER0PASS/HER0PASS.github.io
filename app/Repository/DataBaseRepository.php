<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISession;
use App\Models\APIUser;
use App\Models\TwitchGetTopsofthetops;
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

    public function getAPIUserByEmail(APIUser $apiApiUser): ?APIUser
    {
        $query = DB::table('users')->where('email', $apiApiUser->getEmail());

        if ($apiApiUser->getApiKey() !== null) {
            $query->where('api_key', $apiApiUser->getApiKey());
        }

        $row = $query->first();

        if (!$row) {
            return null;
        }

        return new APIUser($row->id, $row->email, $row->api_key);
    }

    public function updateAPIUserAPIKey(APIUser $apiUser): void
    {
        DB::table('users')
            ->where('email', $apiUser->getEmail())
            ->update(['api_key' => $apiUser->getApiKey()]);
    }

    public function registerAPIUser(APIUser $apiUser): void
    {
        DB::table('users')->updateOrInsert(
            [
            'email' => $apiUser->getEmail()],
            ['api_key' => $apiUser->getApiKey()]
        );
    }

    public function getSessionByToken($token): ?APISession
    {
        $row = DB::table('sessions')
            ->where('token', $token)
            ->first();

        if (!$row) {
            return null;
        }

        return new APISession(
            (int) $row->user_id,
            $row->token,
            new \DateTime($row->expires_at)
        );
    }

    public function getSessionByUserId($user_id): ?APISession
    {
        $row = DB::table('sessions')
            ->where('user_id', $user_id)
            ->first();

        if (!$row) {
            return null;
        }

        return new APISession(
            (int) $row->user_id,
            $row->token,
            new \DateTime($row->expires_at)
        );
    }

    public function registerSession(APISession $apiSession): void
    {
        DB::table('sessions')->insert([
            'user_id' => $apiSession->getUserId(),
            'token' => $apiSession->getToken(),
            'expires_at' => $apiSession->getExpiresAt()
        ]);
    }

    public function updateSession(APISession $apiSession): void
    {
        DB::table('sessions')
            ->where('user_id', $apiSession->getUserId())
            ->update(['token' => $apiSession->getToken(), 'expires_at' => $apiSession->getExpiresAt()]);
    }

    public function getTimestampCache(): ?\DateTime
    {
        $row = DB::table('cache')
            ->orderByDesc('timestamp')
            ->first();

        if (!$row || !$row->timestamp) {
            return null;
        }

        return new \DateTime($row->timestamp);
    }

    public function getTopsofthetops(int $top): ?TwitchGetTopsofthetops
    {
        $row = DB::table('cache')
            ->where('top', $top)
            ->first();

        if (!$row || empty($row->data)) {
            return null;
        }

        $data = json_decode($row->data, true);

        if (!is_array($data)) {
            return null;
        }

        return TwitchGetTopsofthetops::fromArray($data);
    }

    public function saveTopsofthetops(TwitchTopsofthetops $top, int $position): void
    {
        DB::table('cache')->insert([
            'top' => $position,
            'data' => json_encode($top->toArray()),
            'timestamp' => now(), // opcional, MySQL puede autogenerarlo
        ]);
    }
}
