<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource()]
#[ORM\Entity()]
#[UniqueEntity('follower', 'collection')]
class CollectionFollower
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectionsFollowed')]
    public User $follower;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'followers')]
    public Collection $collection;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $hidden;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
