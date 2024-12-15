<?php

namespace App\DoctrineType;

class AttributeValueType extends AbstractEnumType
{
    public const NAME = 'attribute_value_type';

    public static function getEnumsClass(): string
    {
        return \App\Enum\AttributeValueType::class;
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
