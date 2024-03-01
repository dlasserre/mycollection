<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\Gender;
use App\Enum\Role;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Get(
            security: 'is_granted("ROLE_ADMIN") || user === object',
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
    mercure: true
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DateTrait;
    use IdTrait;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Collection::class)]
    public iterable $collections;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Item::class)]
    public iterable $items;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\Column(type: 'gender')]
    #[Assert\NotBlank]
    public Gender $gender;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    public string $firstname;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    public string $lastname;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    public string $email;

    #[Groups(['user:input:USER'])]
    public ?string $plainPassword = null;

    #[Groups(['user:input:USER'])]
    #[ORM\Column(type: 'string')]
    public string $password;

    #[Groups(['user:output:USER', 'user:input:USER'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\DateTime]
    public ?\DateTime $birthdayDate = null;

    #[Groups(['user:output:USER', 'user:input:ROLE_SUPER_ADMIN'])]
    #[ORM\Column(type: 'json', nullable: false)]
    public array $roles;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->collections = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->roles[] = Role::USER; // Default role.
    }

    public function getFullname(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
