<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\BadgeProvider;
use App\Repository\BadgeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/badges',
            security: 'is_granted("PUBLIC_ACCESS")',
            provider: BadgeProvider::class
        )
    ],
    normalizationContext: ['groups' => ['badge']],
    denormalizationContext: ['groups' => ['badge']]
)]
#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'userBadges.user' => 'exact'
])]
class Badge
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'badge', targetEntity: UserBadge::class)]
    public \Doctrine\Common\Collections\Collection $userBadges;

    #[Groups([
        'badge:output:ROLE_USER',
        'user:output:ROLE_USER'
    ])]
    #[ORM\Column(nullable: false)]
    public string $name;

    #[Groups([
        'badge:output:PUBLIC_ACCESS',
        'user:output:ROLE_USER'
    ])]
    #[ORM\Column(nullable: false)]
    public string $color;

    #[Groups([
        'badge:output:PUBLIC_ACCESS',
        'user:output:ROLE_USER'
    ])]
    #[ORM\Column(nullable: true)]
    public ?string $description;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}