<?php

namespace App\Enum;

enum Role
{
    case SUPER_ADMIN;
    case ADMIN;

    case USER;

    case ANONYMOUS;

    public function role(string $entity): string
    {
        return $entity . ':' . $this->getRoleName();
    }

    public function getRoleName(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'ROLE_SUPER_ADMIN',
            self::ADMIN => 'ROLE_ADMIN',
            self::USER => 'USER',
            default => 'ANONYMOUS',
        };
    }

}
