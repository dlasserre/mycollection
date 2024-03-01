<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait DateTrait
{
    #[Assert\DateTime()]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[Assert\DateTime()]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTime $updateAt;

    #[Groups(['always'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['always'])]
    public function getUpdateAt(): \DateTime
    {
        return $this->updateAt;
    }
}
