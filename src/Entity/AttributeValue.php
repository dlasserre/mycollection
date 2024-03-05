<?php

namespace App\Entity;

use App\Enum\AttributeValueType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity()]
class AttributeValue
{
    use IdTrait;
    use DateTrait;

    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'attributeValue')]
    public Attribute $attribute;

    #[Groups([
        'attribute:output:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'attribute_value_type', nullable: false)]
    public AttributeValueType $type;

    #[Groups([
        'attribute:output:ROLE_USER',
        'collection:output:ROLE_USER',
        'item:output:ROLE_USER',
    ])]
    #[ORM\Column(type: 'string', nullable: false)]
    public string $value;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $public = false;
}
