<?php

namespace App\Provider;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class CollectionItemProvider extends AbstractProvider
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
        /** @var ItemRepository $repository */
        $repository = $this->getRepository(Item::class);

        return $this->applyCollectionExtension(
            $repository->getItems($this->getUser()),
            $operation,
            $context
        );
    }
}
