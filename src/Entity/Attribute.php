<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource()]
#[ORM\Entity()]
class Attribute
{
    use DateTrait;
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'privateAttributes')]
    public User $createdBy;

    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'attributes')]
    public iterable $items;

    #[Groups([
        'attribute:output:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: AttributeValue::class)]
    public iterable $attributeValue;

    #[Groups([
        'attribute:output:ROLE_USER',
        'attribute:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'string')]
    public string $name;

    #[Groups([
        'attribute:output:ROLE_USER',
        'attribute:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'text', nullable: true)]
    public string $description = '';

    #[Groups([
        'attribute:output:ROLE_USER',
        'attribute:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }
}
