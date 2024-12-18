<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserBadge
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userBadges')]
    public User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Badge::class, inversedBy: 'userBadges')]
    public Badge $badge;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}