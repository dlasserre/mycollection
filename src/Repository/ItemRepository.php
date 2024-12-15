<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class ItemRepository extends AbstractRepository
{
    public function getName(): string
    {
        return Item::class;
    }

    public function getItems(User $user): QueryBuilder
    {
        $query = $this->createQueryBuilder('i');
        if (!$user->isAdmin()) {
            $query->join('i.createdBy', 'user');
        }
        $query->where($query->expr()->orX('i.public = true', 'i.public = false and user = :user'))
            ->setParameter(':user', $user);

        return $query;
    }
}