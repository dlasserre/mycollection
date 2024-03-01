<?php

namespace App\Enum;

enum AttachmentType
{
    use BaseEnumTrait;

    case IMAGE;

    case FILE;

    case CERTIFICATE;

    case INVOICE;

    case OTHER;

    public function getTypeName(): string
    {
        return match ($this) {
            self::IMAGE => 'Image',
            self::FILE => 'File',
            self::CERTIFICATE => 'Certificate',
            self::INVOICE => 'Invoice',
            default => 'other',
        };
    }
}
