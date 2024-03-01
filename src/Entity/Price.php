<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(mercure: true)]
#[ORM\Entity]
class Price
{
    use IdTrait;
    use DateTrait;

    #[Groups([
        'user:output:USER',
        'item:output:USER',
        'item:input:USER',
    ])]
    #[ORM\Column(type: 'bigint')]
    #[Assert\NotBlank()]
    public int $price;

    #[Groups([
        'user:output:USER',
        'item:output:USER',
        'item:input:USER',
    ])]
    #[ORM\Column(type: 'bigint', options: ['default' => 0])]
    public ?int $crossedPrice = null;
}
