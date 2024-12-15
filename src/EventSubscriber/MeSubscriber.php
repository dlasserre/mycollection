<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\SecurityBundle\Security;

class MeSubscriber implements EventSubscriberInterface
{
    public function __construct(protected Security $security, protected Router $router)
    {
    }

    #[ArrayShape([KernelEvents::REQUEST => 'array'])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['resolveMe', EventPrioritiesAlias::PRE_READ],
        ];
    }

    public function resolveMe(RequestEvent $event): void
    {
        $request = $event->getRequest();
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ('me' === $request->get('id')) {
            $request->attributes->set('id', $user->getId());
        }
    }
}
