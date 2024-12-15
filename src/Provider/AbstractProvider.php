<?php

namespace App\Provider;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractProvider implements ProviderInterface
{
    public function __construct(
        protected Security $security,
        private readonly ManagerRegistry $managerRegistry,
        protected iterable $collectionExtensions,
        protected IriConverterInterface $converter,
    ) {
    }

    protected function applyCollectionExtension(
        QueryBuilder $queryBuilder,
        Operation $operation,
        array $context = []
    ): array|null|PaginatorInterface {
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection(
                $queryBuilder,
                new QueryNameGenerator(),
                $operation->getClass(),
                $operation,
                $context
            );
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult(
                    $operation->getClass(),
                    $operation,
                    $context
                )) {
                $result = $extension->getResult($queryBuilder, $operation->getClass(), $operation, $context);
            }
        }

        $result = $result ?? $queryBuilder->getQuery()->getResult();
        if ($result instanceof PaginatorInterface) {
            return $result;
        }

        return iterator_to_array($result);
    }

    protected function getRepository(string $entity): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entity);
    }

    protected function getUser(): UserInterface|User
    {
        return $this->security->getUser();
    }
}
