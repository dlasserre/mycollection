<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Reaction
{
    use IdTrait;
    use DateTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public User $user;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Item $item = null;

    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Collection $collection = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Message $message = null;

    #[ORM\ManyToOne(targetEntity: WishList::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public ?WishList $wishList = null;

    #[ORM\ManyToOne(targetEntity: WishListItem::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: true)]
    public ?WishListItem $wishListItem = null;

    #[ORM\ManyToOne(targetEntity: Resource::class, inversedBy: 'reactions')]
    public ?Resource $resource;

    #[ORM\Column('reaction', nullable: false)]
    public \App\Enum\Reaction $type;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}