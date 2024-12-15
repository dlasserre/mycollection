<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        dd($event->getThrowable());
    }
}
