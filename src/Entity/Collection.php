<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(mercure: true)]
#[ORM\Entity()]
class Collection
{
    use DateTrait;
    use IdTrait;

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

    #[Orm\ManyToOne(targetEntity: User::class, inversedBy: 'collections')]
    public User $user;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $published;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }
}
