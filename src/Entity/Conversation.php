<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource()]
#[ORM\Entity()]
class Conversation
{
    use IdTrait;
    use DateTrait;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'myConversations')]
    public User $creator;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'conversations')]
    public iterable $users;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class)]
    public iterable $messages;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $title;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }
}
