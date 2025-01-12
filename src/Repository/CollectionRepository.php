<?php

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class CollectionRepository extends AbstractRepository
{
    public function getName(): string
    {
        return Collection::class;
    }

    public function getCollections(User $user): QueryBuilder
    {
        $query = $this->createQueryBuilder('c');
        if (!$user->isAdmin()) {
            $query->join('c.user', 'user');
        }
        $query
            ->where(
                $query->expr()->orX('c.public = false and c.user = :user', 'c.public = true')
            )->setParameter(':user', $user);

        return $query;
    }
}
