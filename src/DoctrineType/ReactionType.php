<?php

namespace App\DoctrineType;

use App\Enum\Reaction;

class ReactionType extends AbstractEnumType
{
    public const NAME = 'reaction';

    public static function getEnumsClass(): string
    {
        return Reaction::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}