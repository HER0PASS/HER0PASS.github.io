<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use App\Models\TwitchUser;
use Illuminate\Support\Facades\DB;

class RealDataBaseRepository implements DataBaseRepositoryInterface
{
    public function getUserById(string $userId): ?TwitchUser
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

    public function saveUser(TwitchUser $user): void
    {
        DB::table('TwitchUsers')->updateOrInsert(
            ['idUser' => $user->getId()],
            ['data' => json_encode($user->toArray())]
        );
    }

    public function getUserByEmail($email): ?APIUser
    {
        $row = DB::table('users')
            ->where('email', $email)
            ->first();

        if (!$row || !$row->data) {
            return null;
        }

        $userData = json_decode($row->data, true);
        return APIUser::fromArray($userData);
    }

    public function checkUserExistence($email, $api_key): ?APIUser
    {
        $row = DB::table('users')
            ->where('email', $email)
            ->where('api_key', $api_key)
            ->first();

        if (!$row || !$row->data) {
            return null;
        }

        $userData = json_decode($row->data, true);
        return APIUser::fromArray($userData);
    }

    public function updateApiKey(string $email, string $api_key): void
    {
        DB::table('users')
            ->where('email', $email)
            ->update(['api_key' => $api_key]);
    }

    public function registerEmailAndApiKey(string $email, string $api_key): void
    {
        DB::table('users')->insert([
            'email' => $email,
            'api_key' => $api_key,
        ]);
    }

    public function getExpireDate($token): ?APISessions
    {
        $row = DB::table('sessions')
            ->where('token', $token)
            ->first();

        if (!$row || !$row->data) {
            return null;
        }

        $sessionData = json_decode($row->data, true);
        return APISessions::fromArray($sessionData);
    }

    public function getTokenFromDatabase($user_id): ?APISessions
    {
        $row = DB::table('sessions')
            ->where('user_id', $user_id)
            ->first();

        if (!$row || !$row->data) {
            return null;
        }

        $sessionData = json_decode($row->data, true);
        return APISessions::fromArray($sessionData);
    }

    public function registerTokenInDatabase($token, $expires_at, $user_id): void
    {
        DB::table('sessions')->insert([
            'user_id' => $user_id,
            'token' => $token,
            'expires_at' => $expires_at
        ]);
    }

    public function updateTokenInDatabase($token, $expires_at, $user_id): void
    {
        DB::table('sessions')
            ->where('user_id', $user_id)
            ->update(['token' => $token, 'expires_at' => $expires_at]);
    }
}
