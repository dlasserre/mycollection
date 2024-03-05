<?php

namespace App\DoctrineType;

use App\Entity\AttributeValue;

class AttributeValueType extends AbstractEnumType
{
    public const NAME = 'attribute_value_type';

    public static function getEnumsClass(): string
    {
        return AttributeValue::class;
    }

    public function getColumnType(): string
    {
        return 'VARCHAR(255)';
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
