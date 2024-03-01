<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping\ManyToOne;

#[ApiResource(mercure: true)]
class ItemBuyer
{
    use IdTrait;
    use DateTrait;

    #[ManyToOne(targetEntity: Item::class)]
    public Item $item;

    #[ManyToOne(targetEntity: Item::class)]
    public User $buyer;

    #[ManyToOne(targetEntity: Item::class)]
    public User $seller;
}
