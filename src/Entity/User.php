<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\Gender;
use App\Enum\Role;
use App\Processor\UserRegisterProcessor;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Get(
            security: 'is_granted("ROLE_ADMIN") || user === object',
        ),
        new Post(
            uriTemplate: '/register',
            security: 'is_granted("PUBLIC_ACCESS")',
            processor: UserRegisterProcessor::class,
        ),
        new Post(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN") || user === object',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")'
        )
    ],
    normalizationContext: ['groups' => ['user']],
    denormalizationContext: ['groups' => ['user']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'partial',
    'firstname' => 'partial',
    'lastname' => 'partial'
])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DateTrait;
    use IdTrait;

    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\Column(type: 'gender')]
    public Gender $gender;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\Column(type: 'string')]
    #[NotBlank]
    public string $firstname;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\Column(type: 'string')]
    #[NotBlank]
    public string $lastname;
    #[ORM\Column(type: 'string')]
    #[NotBlank]
    public string $email;
    #[Groups(['user:input:ROLE_USER'])]
    public ?string $plainPassword = null;
    #[ORM\Column(type: 'string')]
    public string $password;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Item::class)]
    private \Doctrine\Common\Collections\Collection $items;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'users')]
    private \Doctrine\Common\Collections\Collection $privateCategories;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Attribute::class)]
    private \Doctrine\Common\Collections\Collection $privateAttributes;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: CollectionFollower::class)]
    private \Doctrine\Common\Collections\Collection $collectionsFollowed;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WishList::class)]
    private \Doctrine\Common\Collections\Collection $wishLists;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    private \Doctrine\Common\Collections\Collection $conversations;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Conversation::class)]
    private \Doctrine\Common\Collections\Collection $myConversations;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private \Doctrine\Common\Collections\Collection $messages;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reaction::class)]
    private \Doctrine\Common\Collections\Collection $reactions;
    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserBadge::class)]
    private \Doctrine\Common\Collections\Collection $userBadges;
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Collection::class)]
    private \Doctrine\Common\Collections\Collection $collections;
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $birthdayDate = null;

    #[ORM\Column(type: 'json', nullable: false)]
    private array $roles;

    #[ORM\OneToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Image $profileImage = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->collections = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->collectionsFollowed = new ArrayCollection();
        $this->wishLists = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->roles[] = Role::USER; // Default role.
    }

    #[Groups([
        'user:output:ROLE_USER',
        'collection:output:ROLE_USER'
    ])]
    public function getFullName(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function followCollection(Collection $collection, bool $hidden = false): User
    {
        if ($collection->public and !$this->collectionsFollowed->contains($collection)) {
            $collectionFollower = new CollectionFollower();
            $collectionFollower->follower = $this;
            $collectionFollower->collection = $collection;
            $collectionFollower->hidden = $hidden;
            $collection->followers->add($collectionFollower);
            $this->collectionsFollowed->add($collectionFollower);
        }

        return $this;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getPassword(): ?string
    {
        return $this->password;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function hasCollection(Collection $collection): bool
    {
        return $this->collections->contains($collection);
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function isAdmin(): bool
    {
        return in_array(Role::ADMIN, $this->roles);
    }

    #[Groups(['user:output:ROLE_USER'])]
    #[SerializedName('imageProfilePath')]
    public function getImageProfilePath(): ?string
    {
        if ($this->profileImage instanceof Image) {
            return '/public/uploads/images/' . $this->profileImage->filePath;
        }
        return null;
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getCollections(): \Doctrine\Common\Collections\Collection
    {
        return $this->collections->matching(
            Criteria::create()->andWhere(Criteria::expr()->eq('enabled', true))
        );
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getTotalCollections(): int
    {
        return $this->collections->count();
    }

    #[Groups(['user:output:ROLE_USER'])]
    public function getTotalItems(): int
    {
        return $this->items->count();
    }

    #[Groups([
        'user:output:ROLE_USER',
        'collection:output:ROLE_USER',
    ])]
    public function getBadges(): \Doctrine\Common\Collections\Collection
    {
        $badges = new ArrayCollection();
        /** @var UserBadge $userBadge */
        foreach ($this->userBadges as $userBadge) {
            $badges->add($userBadge->badge);
        }
        return $badges;
    }
}
