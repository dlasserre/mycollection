<?php

namespace App\EventListener\DoctrineListener;

use App\Entity\MediaFile;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class MediaFileListener
{
    public function __construct(
        #[Autowire(service: 'security.helper')]
        private Security $security
    ) {
    }

    public function prePersist(MediaFile $mediaFile): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $mediaFile->user = $user;
    }
}