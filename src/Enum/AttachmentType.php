<?php

namespace App\Enum;

enum AttachmentType: string
{
    use BaseEnumTrait;

    case IMAGE = 'image';
    case PP = 'pp';
    case FILE = 'file';
    case CERTIFICATE = 'certificate';
    case INVOICE = 'invoice';
    case OTHER = 'other';
}
