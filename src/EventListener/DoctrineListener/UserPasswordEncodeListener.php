<?php

namespace App\EventListener\DoctrineListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserPasswordEncodeListener
{
    public function __construct(private UserPasswordHasherInterface $encoder)
    {
    }

    public function prePersist(User $user): void
    {
        $user->password = $this->encoder->hashPassword($user, $user->plainPassword);
    }
}
