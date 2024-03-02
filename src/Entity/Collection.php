<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Processor\CollectionProcessor;
use App\Provider\CollectionItemProvider;
use App\Provider\CollectionProvider;
use App\Repository\CollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")',
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
    ]
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
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'children')]
    public ?Collection $parent = null;

    #[Groups([
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Collection::class)]
    public iterable $children;

    #[Groups([
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'collections')]
    public iterable $items;

    #[Groups([
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Attachment::class)]
    public iterable $attachments;

    #[Groups([
        'user:output:USER',
        'item:output:USER',
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Groups([
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[Orm\ManyToOne(targetEntity: User::class, inversedBy: 'collections')]
    public User $user;

    #[Assert\NotNull()]
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
    }
}
