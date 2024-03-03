<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object.collection ?? object.item)',
        ),
        new Post(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object.collection ?? object.item)',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object.collection ?? object.item)',
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN") or user.hasCollection(object.collection ?? object.item)',
        )
    ]
)]
#[Entity]
class Resource
{
    use IdTrait;
    use DateTrait;

    #[ManyToOne(targetEntity: Collection::class, inversedBy: 'resources')]
    #[JoinColumn(nullable: true)]
    public ?Collection $collection = null;

    #[ManyToOne(targetEntity: Item::class, inversedBy: 'resources')]
    #[JoinColumn(nullable: true)]
    public ?Item $item = null;

    #[Groups([
        'item:output:USER',
        'item:input:USER',
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[Column(type: 'string', nullable: false)]
    public string $url;

    #[Groups([
        'item:output:USER',
        'item:input:USER',
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[Column(type: 'string', nullable: false)]
    public string $name;

    #[Groups([
        'item:output:USER',
        'item:input:USER',
        'collection:output:USER',
        'collection:input:USER',
    ])]
    #[Column(type: 'text', nullable: false)]
    public ?string $description = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
