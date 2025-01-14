<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Filter\UserFilter;
use App\Processor\CollectionProcessor;
use App\Provider\CollectionProvider;
use App\Repository\CollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: CollectionProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object) or object.isPublic()',
        ),
        new Post(
            security: 'is_granted("ROLE_USER")',
            processor: CollectionProcessor::class
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") and user.hasCollection(object)',
            processor: CollectionProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") and user.hasCollection(object)',
            processor: CollectionProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['collection']],
    denormalizationContext: ['groups' => ['collection']],
    filters: [
        UserFilter::class
    ],
    paginationClientItemsPerPage: true
)]
#[ORM\Entity(repositoryClass: CollectionRepository::class)]
#[ApiFilter(BooleanFilter::class, properties: [
    'public'
])]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'category.name' => 'exact',
    'user' => 'exact',
])]
class Collection
{
    use DateTrait;
    use IdTrait;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Attachment::class)]
    public \Doctrine\Common\Collections\Collection $attachments;
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Resource::class)]
    public \Doctrine\Common\Collections\Collection $resources;
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: CollectionFollower::class, cascade: ['persist'])]
    public \Doctrine\Common\Collections\Collection $followers;
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Reaction::class)]
    public \Doctrine\Common\Collections\Collection $reactions;
    #[ORM\OneToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Image $image = null;
    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'collections')]
    public Category $category;
    #[Groups([
        'user:output:ROLE_USER',
        'item:output:ROLE_USER',
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
        'category:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    public string $name;
    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;
    #[Orm\ManyToOne(targetEntity: User::class, inversedBy: 'collections')]
    public User $user;
    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public;
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    public bool $enabled = true;
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Collection::class)]
    private \Doctrine\Common\Collections\Collection $children;
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'collections')]
    private \Doctrine\Common\Collections\Collection $items;
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'children')]
    private ?Collection $parent = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->children = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    #[Groups([
        'collection:input:ROLE_USER',
    ])]
    public function setChildrenCollections(iterable $children): Collection
    {
        /** @var Collection $child */
        foreach ($children as $child) {
            $child->user = $this->user;
            $this->addChildrenCollection($child);
        }

        return $this;
    }

    public function addChildrenCollection(Collection $collection): Collection
    {
        if (!$this->children->contains($collection)) {
            $this->children->add($collection);
        }

        return $this;
    }

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    public function getChildrenCollections(): iterable
    {
        return $this->children;
    }

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    public function getItems(): iterable
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('public', true))
            ->setMaxResults(10);
        return $this->items->matching($criteria);
    }

    #[Groups([
        'collection:input:ROLE_USER',
    ])]
    public function setItems(iterable $items): Collection
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    public function addItem(Item $item): Collection
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
        return $this;
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    #[Context(['security' => 'is_granted("ROLE_USER") && user != object.user'])]
    #[SerializedName('totalItems')]
    public function getTotalPublicItems(): int
    {
        return $this->items->matching(
            Criteria::create()->andWhere(Criteria::expr()->eq('public', true))
        )->count();
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    #[Context(['security' => 'is_granted("ROLE_USER") && user == object.user'])]
    public function getTotalItems(): int
    {
        return $this->items->count();
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    #[Context(['security' => 'is_granted("ROLE_USER") && user == object.user'])]
    public function getTotalPrivateItems(): int
    {
        return $this->items->matching(
            Criteria::create()->andWhere(Criteria::expr()->eq('public', false))
        )->count();
    }

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    public function getParent(): ?Collection
    {
        return $this->parent;
    }

    #[Groups([
        'collection:input:ROLE_USER',
    ])]
    public function setParent(Collection $collection): Collection
    {
        $this->user = $collection->user;
        $this->parent = $collection;
        return $this;
    }

    public function addFollower(User $user, bool $hidden = false): Collection
    {
        $user->followCollection($this, $hidden);
        return $this;
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    public function getTotalFollowers(): int
    {
        return $this->followers->count();
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    public function getTotalLikes(): int
    {
        return $this->reactions->matching(
            Criteria::create()->andWhere(Criteria::expr()->eq('type', \App\Enum\Reaction::LIKE))
        )->count();
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    public function isPublic(): bool
    {
        return $this->public;
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'user:output:ROLE_USER',
    ])]
    public function getType(): string
    {
        return $this->category?->parent->name ?? $this->category->name;
    }

    #[Groups(['collection:output:ROLE_USER', 'user:output:ROLE_USER'])]
    public function getImage(): ?Image
    {
        return $this->image;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getShortDescription(): string
    {
        return substr($this->description, 0, 100) . '...';
    }
}
