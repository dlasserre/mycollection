<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['always'])]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): mixed
    {
        $this->id = $id;
        return $this;
    }
}
