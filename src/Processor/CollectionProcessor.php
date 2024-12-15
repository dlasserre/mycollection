<?php

namespace App\Processor;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CollectionProcessor extends AbstractProcessor
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
     * @param Collection $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function post(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Collection
    {
        if ($data->getParent() instanceof Collection and $data->getParent()->user !== $this->getUser()) {
            throw new UnauthorizedHttpException('', 'Your not allowed to use this parent collection');
        }
        $data->user = $this->getUser();
        $this->save($data);
        return $data;
    }

    public function delete($data, array $context = []): ?Collection
    {
        if ($this->getUser()->isAdmin()) {
            $this->delete($data);
            return null;
        }
        $data->enabled = false;
        $this->save($data);
        return $data;
    }
}
