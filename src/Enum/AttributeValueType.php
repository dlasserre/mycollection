<?php

namespace App\Enum;

enum AttributeValueType: string
{
    use BaseEnumTrait;

    case INTEGER = 'integer';
    case DOUBLE = 'double';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
}
