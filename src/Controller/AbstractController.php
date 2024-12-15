<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly Security $security,
        private readonly RequestStack $requestStack
    ) {
    }

    public function getRepository(mixed $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }

    public function save(mixed $entity): void
    {
        $this->managerRegistry->getManager()->persist($entity);
        $this->managerRegistry->getManager()->flush();
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }
}
