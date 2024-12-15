<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity()]
class WishListItem
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'wishListItem', targetEntity: Reaction::class)]
    public iterable $reactions;

    #[ManyToOne(targetEntity: WishList::class, inversedBy: 'items')]
    public WishList $wishList;

    #[ManyToOne(targetEntity: Item::class, inversedBy: 'wishes')]
    public Item $item;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Price $price = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reactions = new ArrayCollection();
    }
}
