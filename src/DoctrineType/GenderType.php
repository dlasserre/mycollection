<?php

namespace App\DoctrineType;

use App\Enum\Gender;

class GenderType extends AbstractEnumType
{
    public const NAME = 'gender';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return Gender::class;
    }
}
