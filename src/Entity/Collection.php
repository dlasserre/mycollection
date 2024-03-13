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
use App\Processor\CollectionProcessor;
use App\Provider\CollectionProvider;
use App\Repository\CollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_USER")',
            provider: CollectionProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object)',
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
        )
    ],
    normalizationContext: ['groups' => ['collection']],
    denormalizationContext: ['groups' => ['collection']]
)]
#[ORM\Entity(repositoryClass: CollectionRepository::class)]
#[ApiFilter(BooleanFilter::class, properties: [
    'published'
])]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial'
])]
class Collection
{
    use DateTrait;
    use IdTrait;

    /**
     * @todo refactor : https://perso.univ-lemans.fr/~cpiau/BD/SQL_PAGES/SQL13.htm
     *  https://github.com/dan-on/php-interval-tree
     *  https://github.com/judev/php-intervaltree
     * */
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'children')]
    private ?Collection $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Collection::class)]
    private iterable $children;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'collections')]
    public Category $category;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'collections')]
    private iterable $items;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Attachment::class)]
    public iterable $attachments;

    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Resource::class)]
    public iterable $resources;

    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: CollectionFollower::class, cascade: ['persist'])]
    public iterable $followers;

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

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $published;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    public bool $enabled = true;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->createdAt =  new \DateTime();
        $this->children = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function addChildrenCollection(Collection $collection): Collection
    {
        if (!$this->children->contains($collection)) {
            $this->children->add($collection);
        }

        return $this;
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

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    public function getChildrenCollections(): iterable
    {
        return $this->children;
    }

    public function addItem(Item $item): Collection
    {
        if(!$this->items->contains($item)) {
            $this->items->add($item);
        }
        return $this;
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
        'collection:output:ROLE_USER',
    ])]
    public function getTotalItems(): int
    {
        return $this->items->count();
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

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    public function getParent(): ?Collection
    {
        return $this->parent;
    }

    public function addFollower(User $user, bool $hidden = false): Collection
    {
        $user->followCollection($this, $hidden);
        return $this;
    }
}
