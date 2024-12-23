<?php

namespace App\Processor;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;

#[AutoconfigureTag('api_platform.state_processor')]
class UserRegisterProcessor extends AbstractProcessor
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
     * @param User $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return User
     */
    public function post(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): User {
        $this->save($data);
        return $data;
    }
}