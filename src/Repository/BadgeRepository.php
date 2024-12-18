<?php

namespace App\Repository;

use App\Entity\Badge;
use Doctrine\ORM\QueryBuilder;

class BadgeRepository  extends AbstractRepository
{
    public function getName(): string
    {
        return Badge::class;
    }

    public function getBadges(): QueryBuilder
    {
        return $this->createQueryBuilder('b');
    }
}