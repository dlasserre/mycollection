<?php

namespace App\EventListener\DoctrineListener;

class UpdateDateListener
{
    public function preUpdate(mixed $entity): void
    {
        if (method_exists($entity, 'setUpdateAt')) {
            $entity->setUpdateAt();
        }
    }
}
