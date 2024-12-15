<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Message
{
    use IdTrait;
    use DateTrait;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: Reaction::class)]
    public iterable $reactions;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    public Conversation $conversation;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    public User $sender;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $content;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->reactions = new ArrayCollection();
    }
}
