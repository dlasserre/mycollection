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
    normalizationContext: ['groups' => ['user:output:ROLE_USER']],
    denormalizationContext: ['groups' => ['user:input:ROLE_USER']]
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

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserBadge::class)]
    public \Doctrine\Common\Collections\Collection $userBadges;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Collection::class)]
    public \Doctrine\Common\Collections\Collection $collections;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Item::class)]
    public \Doctrine\Common\Collections\Collection $items;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'users')]
    public \Doctrine\Common\Collections\Collection $privateCategories;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Attribute::class)]
    public \Doctrine\Common\Collections\Collection $privateAttributes;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: CollectionFollower::class)]
    public \Doctrine\Common\Collections\Collection $collectionsFollowed;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WishList::class)]
    public \Doctrine\Common\Collections\Collection $wishLists;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    public \Doctrine\Common\Collections\Collection $conversations;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Conversation::class)]
    public \Doctrine\Common\Collections\Collection $myConversations;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    public \Doctrine\Common\Collections\Collection $messages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reaction::class)]
    public \Doctrine\Common\Collections\Collection $reactions;

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

    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\Column(type: 'string')]
    #[NotBlank]
    public string $email;

    #[Groups(['user:input:ROLE_USER'])]
    public ?string $plainPassword = null;

    #[ORM\Column(type: 'string')]
    public string $password;

    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $birthdayDate = null;

    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_SUPER_ADMIN'])]
    #[ORM\Column(type: 'json', nullable: false)]
    public array $roles;

    #[Groups(['user:output:ROLE_USER', 'user:input:ROLE_USER'])]
    #[ORM\OneToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(nullable: true)]
    public ?Image $profileImage = null;

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

    public function getFullName(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function hasCollection(Collection $collection): bool
    {
        return $this->collections->contains($collection);
    }

    public function isAdmin(): bool
    {
        return in_array(Role::ADMIN, $this->roles);
    }

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

    #[Groups(['user:output:ROLE_USER'])]
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
