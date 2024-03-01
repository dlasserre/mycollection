<?php

namespace App\Enum;

trait BaseEnumTrait
{
    public static function from(string $name): mixed
    {
        $name = strtoupper($name);

        return constant("self::$name");
    }

    public function equal(mixed $status): bool
    {
        return $this === $status;
    }

    public function in(array $allowed): bool
    {
        return in_array($this, $allowed);
    }
}
