<?php

namespace App\Enum;

enum Role: string
{
    use BaseEnumTrait;
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'USER';
    case ANONYMOUS = 'ANONYMOUS';
}
