<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Provider\CategoryProvider;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(operations: [
    new GetCollection(
        security: 'is_granted("ROLE_USER")',
        provider: CategoryProvider::class
    ),
    new Get(
        security: 'is_granted("ROLE_ADMIN") or (is_granted("ROLE_USER") and object.isPublic) or (is_granted("ROLE_USER") and object.hasUser(user))'
    ),
    new Post(
        security: 'is_granted("ROLE_USER")'
    )
    ],
    normalizationContext: ['groups' => ['category']],
    denormalizationContext: ['groups' => ['category']],
)]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use IdTrait;
    use DateTrait;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    public ?Category $parent = null;

    #[Groups([
        'collection:output:ROLE_USER',
    ])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Category::class)]
    public iterable $children;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'privateCategories')]
    public iterable $users;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Collection::class)]
    private iterable $collections;

    #[Groups([
        'collection:output:ROLE_USER',
        'category:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'string')]
    public string $name;

    #[ORM\Column(type: 'boolean')]
    public bool $public = false;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->collections = new ArrayCollection();
    }

    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    public function isPublic():bool
    {
        return $this->public;
    }
    
    public function getCollections(): \Doctrine\Common\Collections\Collection
    {
        $criteria = Criteria::create()->andWhere(
            Criteria::expr()->andX(
                Criteria::expr()->eq('published', 1),
                Criteria::expr()->eq('enabled', 1)
            )
        );
        return $this->collections->matching($criteria);
    }

    #[Groups([
        'collection:output:ROLE_USER',
        'category:output:ROLE_USER',
    ])]
    #[SerializedName('totalPublicCollections')]
    public function getTotalCollections():int
    {
        return $this->getCollections()->count();
    }
}
