<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ApiResource(
    types: ['https://schema.org/MediaFile'],
    operations: [
        new Get(
            uriTemplate: '/media_files/{id}',
        ),
        new GetCollection(),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_USER") && object.user == user',
        )
    ],
    outputFormats: ['jsonld' => ['application/ld+json']],
    normalizationContext: ['groups' => ['media_file']],
)]
#[ORM\Entity]
class MediaFile
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'mediaFiles')]
    public User $user;

    #[Vich\UploadableField(mapping: 'media_file', fileNameProperty: 'filePath')]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;

    #[Groups(['media_file'])]
    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    public string $contentUrl;
}