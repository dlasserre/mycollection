<?php

namespace App\Provider;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

class BadgeProvider extends AbstractProvider
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
        /** @var BadgeRepository $repository */
        $repository = $this->getRepository(Badge::class);

        return $this->applyCollectionExtension(
            $repository->getBadges(),
            $operation,
            $context
        );
    }

}