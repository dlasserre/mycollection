<?php

namespace App\Enum;

enum Gender: string
{
    use BaseEnumTrait;

    case FEMALE = 'female';
    case MALE = 'male';
}
