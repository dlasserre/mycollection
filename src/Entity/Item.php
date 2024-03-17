<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Filter\UserFilter;
use App\Processor\ItemProcessor;
use App\Provider\CollectionItemProvider;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: CollectionItemProvider::class
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
    denormalizationContext: ['groups' => ['item']],
    filters: [
        UserFilter::class
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'description' => 'partial',
])]
#[ApiFilter(RangeFilter::class, properties: [
    'price.price'
])]
#[ApiFilter(DateFilter::class, strategy: DateFilterInterface::EXCLUDE_NULL, properties: [
    'createdAt',
    'updatedAt'
])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
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
        'item:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Attachment::class)]
    public ?iterable $attachments = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Resource::class)]
    public ?iterable $resources = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: WishListItem::class)]
    public iterable $wishes;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Reaction::class)]
    public iterable $reactions;

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
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Price::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Price $price = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->collections = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->wishes = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function isBuyable(): bool
    {
        return $this->price instanceof Price;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    #[Groups([
        'collection:input:ROLE_USER',
        'item:input:ROLE_USER',
    ])]
    public function setPrices(Price $price): Item
    {
        $this->price = $price;
        return $this;
    }

    public function addCollection(Collection $collection): Item
    {
        if (!$this->collections->contains($collection)) {
            $collection->addItem($this);
            $this->createdBy = $collection->user;
            $this->collections->add($collection);
        }

        return $this;
    }
}
