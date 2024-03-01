<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(mercure: true)]
#[ORM\Entity()]
class Attribute
{
    use DateTrait;
    use IdTrait;

    #[ORM\OneToMany(mappedBy: 'attributes', targetEntity: Item::class)]
    public iterable $items;

    #[Groups([
        'attribute:output:USER',
        'attribute:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Groups([
        'attribute:output:USER',
        'attribute:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    public string $value;

    #[Groups([
        'attribute:output:USER',
        'attribute:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'text', nullable: true)]
    public string $description = '';

    #[Groups([
        'attribute:output:USER',
        'attribute:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }
}
