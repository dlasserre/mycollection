<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DateTrait
{
    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updateAt = null;

    #[Groups(['always'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['always'])]
    public function getUpdateAt(): ?\DateTime
    {
        return $this->updateAt;
    }

    public function setUpdateAt(): void
    {
        $this->updateAt = new \DateTime();
    }
}
