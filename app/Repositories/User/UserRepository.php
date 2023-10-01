<?php

namespace App\Repositories\User;

use App\Enums\DelFlg;
use App\Enums\UserFlg;
use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    /**
     * Find user by email
     *
     * @param mixed $email
     * @return \App\Models\User
     */
    public function findUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

}
