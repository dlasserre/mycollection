<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateImageObjectAction;
use App\Enum\AttachmentType;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(operations: [
    new Get(),
    new Delete(security: 'is_granted("ROLE_ADMIN")'),
    new GetCollection(paginationFetchJoinCollection: true),
    new Post(
        controller: CreateImageObjectAction::class,
        deserialize: false
    ),
], normalizationContext: ['groups' => ['mediaInvoice']])]
#[ORM\Entity]
#[Vich\Uploadable]
class Image
{
    use IdTrait;

    #[ApiProperty(iris: ['https://schema.org/contentUrl'])]
    public mixed $contentUrl;

    #[Vich\UploadableField(mapping: 'image', fileNameProperty: 'filePath')]
    public mixed $file;

    #[ORM\Column(nullable: true)]
    public mixed $filePath;

    #[ORM\Column(type: 'attachment_type', nullable: false)]
    public AttachmentType $type;


}
