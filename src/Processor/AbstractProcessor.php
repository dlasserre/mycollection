<?php

namespace App\Processor;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Collection;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function __construct(
        protected Security $security,
        private readonly ManagerRegistry $managerRegistry,
        protected IriConverterInterface $converter,
        private readonly RequestStack $requestStack
    ) {
    }

    abstract public function post(mixed $data, Operation $operation, array $uriVariables = [], array $context = []);
    abstract public function delete(mixed $data, array $context = []);
    abstract public function patch(mixed $data, Operation $operation, array $uriVariables = [], array $context = []);

    /**
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): ?Collection
    {
        return match ($method = $this->getRequestMethod()) {
            Request::METHOD_DELETE => $this->delete($data, $context),
            Request::METHOD_PATCH => $this->patch($data, $operation, $uriVariables, $context),
            Request::METHOD_POST => $this->post($data, $operation, $uriVariables, $context),
            default => throw new \Exception($method.' not found or not allowed on this resource')
        };
    }

    protected function getRepository(string $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }

    protected function getUser(): UserInterface|User
    {
        return $this->security->getUser();
    }

    public function save(mixed $entity, bool $flush = true): void
    {
        $this->managerRegistry->getManager()->persist($entity);
        if ($flush) {
            $this->managerRegistry->getManager()->flush();
        }
    }

    public function remove(mixed $entity): void
    {
        $this->managerRegistry->getManager()->remove($entity);
        $this->managerRegistry->getManager()->flush();
    }

    public function getRequestMethod(): string
    {
        return $this->requestStack->getCurrentRequest()->getMethod();
    }
}
