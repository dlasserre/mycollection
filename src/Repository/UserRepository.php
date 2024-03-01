<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends AbstractRepository
{
    public function getName(): string
    {
        return User::class;
    }
}
