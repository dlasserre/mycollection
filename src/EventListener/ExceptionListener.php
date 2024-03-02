<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        dd($exception->getMessage(), $exception->getFile(), $exception->getLine());
    }
}
