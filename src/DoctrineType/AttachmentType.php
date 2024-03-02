<?php

namespace App\DoctrineType;

use App\Enum\AttachmentType as Attachment;

class AttachmentType extends AbstractEnumType
{
    public const NAME = 'attachment';

    public static function getEnumsClass(): string
    {
        return Attachment::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
