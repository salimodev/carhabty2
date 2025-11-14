<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Users $User = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(type:"boolean")]
    private bool $isRead = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

  #[ORM\ManyToOne(targetEntity: Offre::class)]
#[ORM\JoinColumn(onDelete: "CASCADE", nullable: true)]
private ?Offre $offre = null;


public function getOffre(): ?Offre
{
    return $this->offre;
}

public function setOffre(?Offre $offre): static
{
    $this->offre = $offre;
    return $this;
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->User;
    }

    public function setUser(?Users $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
