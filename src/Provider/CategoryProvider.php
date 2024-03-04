<?php

namespace App\Provider;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class CategoryProvider extends AbstractProvider
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
        /** @var CategoryRepository $repository */
        $repository = $this->getRepository(Category::class);

        return $this->applyCollectionExtension(
            $repository->getCategories($this->getUser()),
            $operation, 
            $context
        );
    }
}
