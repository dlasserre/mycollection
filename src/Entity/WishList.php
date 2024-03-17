<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class WishList
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'wishList', targetEntity: WishListItem::class, cascade: ['persist', 'remove'])]
    public iterable $items;

    #[ORM\OneToMany(mappedBy: 'wishList', targetEntity: Reaction::class)]
    public iterable $reactions;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wishLists')]
    public User $user;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->items = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function addItem(WishListItem $item): WishList
    {
        if ($this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }
}
