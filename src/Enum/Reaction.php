<?php

namespace App\Enum;

enum Reaction: string
{
    use BaseEnumTrait;

    case LIKE = 'like';
    case DISLIKE = 'dislike';
}