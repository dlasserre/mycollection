<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\AttachmentType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity]
class Attachment
{
    use DateTrait;
    use IdTrait;

    #[Groups([
        'attachment:output:USER',
        'attachment:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'attachment_type')]
    #[Assert\NotBlank()]
    public AttachmentType $type;

    #[Groups([
        'attachment:output:USER',
        'attachment:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Item $item = null;

    #[Groups([
        'attachment:output:USER',
        'attachment:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\ManyToOne(targetEntity: Collection::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: true)]
    public ?Collection $collection = null;

    #[ORM\OneToOne(targetEntity: MediaFile::class, cascade: ['persist', 'remove'])]
    public MediaFile $file;

    #[Groups([
        'attachment:output:USER',
        'attachment:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    public string $title;

    #[Groups([
        'attachment:output:USER',
        'attachment:input:USER',
        'collection:output:USER',
        'item:output:USER',
    ])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;
}
