<?php

namespace App\Serializer;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Enum\Role;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final readonly class GenericContextBuilder implements SerializerContextBuilderInterface
{
    private const INPUT = 'input';
    private const OUTPUT = 'output';

    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private Security $security,
        private RoleHierarchyInterface $hierarchy
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $user = $this->security->getUser();

        if (array_key_exists('groups', $context)) {
            $roles = (null !== $user) ?
                $this->hierarchy->getReachableRoleNames($user->getRoles()) : [Role::ANONYMOUS->getRoleName()];
            $context['groups'] = $this->getGroups($normalization, $context, $roles);
        }
        return $context;
    }

    public function getGroups(bool $normalization, array $context, array $roles): array
    {
        $norm = $normalization ? self::OUTPUT : self::INPUT;
        $type = $context['operation'] instanceof GetCollection ? 'collection' : 'item';
        $groups = [
            ['always'],
            [$norm],
            [$type],
            [$norm, $type]
        ];
        foreach ($context['groups'] as $group) {
            array_push($groups, [$group], [$group, $norm], [$group, $type], [$group, $norm, $type]);
            foreach ($roles as $role) {
                array_push(
                    $groups,
                    [$role],
                    [$group, $role],
                    [$norm, $role],
                    [$type, $role],
                    [$norm, $type, $role],
                    [$group, $norm, $role],
                    [$group, $type, $role],
                    [$group, $norm, $type, $role]
                );
            }
        }
        return array_map(function ($directive) {
            return implode(':', $directive);
        }, $groups);
    }
}
