<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Processor\ItemProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
        ),
        new Get(
            security: 'is_granted("ROLE_USER")',
        ),
        new Post(
            security: 'is_granted("ROLE_USER")',
            processor: ItemProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_USER")',
        ),
        new Patch(
            security: 'is_granted("ROLE_USER")',
        ),
    ],
    normalizationContext: ['groups' => ['item']],
    denormalizationContext: ['groups' => ['item']]
)]
#[ORM\Entity]
class Item
{
    use DateTrait;
    use IdTrait;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\ManyToMany(targetEntity: Collection::class, mappedBy: 'items')]
    public iterable $collections;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\ManyToMany(targetEntity: Attribute::class, inversedBy: 'items')]
    public ?iterable $attributes = null;

    #[Groups([
        'item:output:USER',
        'collection:input:USER',
        'collection:output:USER',
        'item:input:USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Attachment::class)]
    public ?iterable $attachments = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Resource::class)]
    public ?iterable $resources = null;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'items')]
    public User $createdBy;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'string')]
    public string $title;

    #[Groups([
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'text')]
    public string $description;

    #[Groups([
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Price::class, cascade: ['persist', 'remove'])]
    public ?iterable $prices = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->collections = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    public function isBuyable(): bool
    {
        return $this->prices->count();
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function addPrice(Price $price): Item
    {
        if (!$this->prices->contains($price)) {
            $price->item = $this;
            $this->prices->add($price);
        }

        return $this;
    }

    #[Groups([
        'collection:input:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    public function setPrices(iterable $prices): Item
    {
        /** @var Price $price */
        foreach ($prices as $price) {
            $this->addPrice($price);
        }
        return $this;
    }
}
