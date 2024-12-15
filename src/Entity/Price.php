<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource()]
#[ORM\Entity]
class Price
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'price', targetEntity: Item::class)]
    public iterable $items;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'integer')]
    public int $price;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $crossedPrice = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
