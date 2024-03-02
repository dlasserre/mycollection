<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(mercure: true)]
#[ORM\Entity]
class Item
{
    use DateTrait;
    use IdTrait;

    #[Groups([
        'user:output:USER',
        'item:output:USER',
        'item:input:USER',
    ])]
    #[ORM\ManyToMany(targetEntity: Collection::class, mappedBy: 'items')]
    public iterable $collections;

    #[Groups([
        'user:output:USER',
        'item:output:USER',
        'item:input:USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'items')]
    public iterable $attributes;

    #[Groups([
        'item:output:USER',
        'collection:input:USER',
        'collection:output:USER',
        'item:input:USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Attachment::class)]
    public iterable $attachments;

    #[Groups([
        'item:output:USER',
        'item:input:USER',
        'collection:output:ADMIN',
    ])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'items')]
    public User $createdBy;

    #[Groups([
        'item:output:USER',
        'collection:input:USER',
        'collection:output:USER',
        'item:input:USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    public string $title;

    #[Groups([
        'item:output:USER',
        'collection:input:USER',
        'collection:output:USER',
        'item:input:USER',
    ])]
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank()]
    public string $description;

    #[ORM\OneToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Price $price = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public = false;

    public function __construct()
    {
        $this->collections = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    public function isBuyable(): bool
    {
        return $this->price instanceof Price;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }
}
