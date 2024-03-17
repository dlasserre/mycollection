<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Collection;
use App\Entity\Item;
use Doctrine\ORM\QueryBuilder;

final class UserFilter extends AbstractFilter
{
    /**
     * @throws \Exception
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if (!$this->isPropertyEnabled($property, $resourceClass)) {
            return;
        }
        list($alias, $field, $needJoin) = match ($resourceClass) {
            Item::class => ['i', 'createdBy', false],
            Collection::class => ['c', 'user', false]
        };
        if (empty($alias) or empty($field)) {
            throw new \Exception('Unknown alias or field');
        }
        if ($needJoin) {
            $queryBuilder->join(sprintf('%s.%s', $alias, $field), 'user');
        }
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('user.email', ':query'),
                $queryBuilder->expr()->like('user.firstname', ':query'),
                $queryBuilder->expr()->like('user.lastname', ':query'),
            )
        )->setParameter(':query', '%' . $value . '%');
    }

    public function isPropertyEnabled(string $property, string $resourceClass): bool
    {
        return 'user.information' === $property;
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}