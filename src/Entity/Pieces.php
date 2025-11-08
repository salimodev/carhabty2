<?php

namespace App\Entity;

use App\Repository\PiecesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PiecesRepository::class)]
class Pieces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column(length: 255)]
    private ?string $referance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datecreateAt = null;

    #[ORM\ManyToOne(inversedBy: 'Pieces')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Demande $demande = null;

    /**
     * @var Collection<int, OffrePiece>
     */
    #[ORM\OneToMany(targetEntity: OffrePiece::class, mappedBy: 'piece')]
    private Collection $offrePieces;

    public function __construct()
    {
        $this->offrePieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getReferance(): ?string
    {
        return $this->referance;
    }

    public function setReferance(string $referance): static
    {
        $this->referance = $referance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getDatecreateAt(): ?\DateTimeImmutable
    {
        return $this->datecreateAt;
    }

    public function setDatecreateAt(\DateTimeImmutable $datecreateAt): static
    {
        $this->datecreateAt = $datecreateAt;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): static
    {
        $this->demande = $demande;

        return $this;
    }

    /**
     * @return Collection<int, OffrePiece>
     */
    public function getOffrePieces(): Collection
    {
        return $this->offrePieces;
    }

    public function addOffrePiece(OffrePiece $offrePiece): static
    {
        if (!$this->offrePieces->contains($offrePiece)) {
            $this->offrePieces->add($offrePiece);
            $offrePiece->setPiece($this);
        }

        return $this;
    }

    public function removeOffrePiece(OffrePiece $offrePiece): static
    {
        if ($this->offrePieces->removeElement($offrePiece)) {
            // set the owning side to null (unless already changed)
            if ($offrePiece->getPiece() === $this) {
                $offrePiece->setPiece(null);
            }
        }

        return $this;
    }
}
