<?php

namespace App\DoctrineType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    abstract public static function getEnumsClass(): string;

    protected function getColumnType(): string
    {
        return 'TEXT';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $this->getColumnType();
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }
        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $enumClass = $this->getEnumsClass();
        if (false === enum_exists($enumClass)) {
            throw new \LogicException(
                sprintf("This class '%s' should be an enum", $this->getEnumsClass()));
        }

        return $this::getEnumsClass()::tryFrom($value);
    }
}
