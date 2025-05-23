<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\TwitchUser;
use Illuminate\Support\Facades\DB;

class DataBaseDBRepository implements DataBaseRepositoryInterface
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
}
