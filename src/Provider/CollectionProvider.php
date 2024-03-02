<?php

namespace App\Provider;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class CollectionProvider extends AbstractProvider
{
    public function __construct(
        protected Security $security,
        protected ManagerRegistry $managerRegistry,
        protected iterable $collectionExtensions,
        protected IriConverterInterface $converter,
    ) {
        parent::__construct($this->security, $this->managerRegistry, $this->collectionExtensions, $this->converter);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var CollectionRepository $repository */
        $repository = $this->getRepository(Collection::class);

        return $this->applyCollectionExtension(
            $repository->getCollections($this->getUser()),
            $operation,
            $context
        );
    }
}
