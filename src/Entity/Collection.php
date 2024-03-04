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
    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'children')]
    public ?Collection $parent = null;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Collection::class)]
    public \Doctrine\Common\Collections\Collection $children;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'collections')]
    public Category $category;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'collections')]
    public \Doctrine\Common\Collections\Collection $items;

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Attachment::class)]
    public \Doctrine\Common\Collections\Collection $attachments;

    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Resource::class)]
    public \Doctrine\Common\Collections\Collection $resources;

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
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'collection:input:ROLE_USER',
    ])]
    public function getItems(): \Doctrine\Common\Collections\Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('public', 1));
        return $this->items->matching($criteria);
    }
}
