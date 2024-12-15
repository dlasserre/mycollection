<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class CategoryRepository extends AbstractRepository
{
    public function getName(): string
    {
        return Category::class;
    }

    public function getCategories(User $user): QueryBuilder
    {
        $query = $this->createQueryBuilder('c');
        $query->leftJoin('c.users', 'user')
            ->where($query->expr()->orX('c.public = true', '(user.id != :null and user = :user)'))
                ->setParameter(':user', $user)
                ->setParameter(':null', 'NULL');
        return $query;
    }
}
