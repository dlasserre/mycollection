<?php

namespace App\Processor;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Collection;
use App\Entity\Item;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class ItemProcessor extends AbstractProcessor
{
    public function __construct(
        Security $security,
        ManagerRegistry $managerRegistry,
        IriConverterInterface $converter,
        RequestStack $requestStack
    ) {
        parent::__construct($security, $managerRegistry, $converter, $requestStack);
    }

    /**
     * @param Item $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Collection
     */
    public function post(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Item
    {
        $data->createdBy = $this->getUser();
        $this->save($data);
        return $data;
    }
}
