<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class WishList
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'wishList', targetEntity: WishListItem::class)]
    public iterable $items;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wishLists')]
    public User $user;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
