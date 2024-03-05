<?php

namespace App\Enum;

enum AttributeValueType: string
{
    case INTEGER = 'integer';
    case DOUBLE = 'double';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
}
