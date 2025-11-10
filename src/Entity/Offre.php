<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroOffre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Demande $demande = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $user = null;

    #[ORM\OneToMany(targetEntity: OffrePiece::class, mappedBy: 'offre', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $offrePieces;

 #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
private ?\DateTimeImmutable $validiteDebut = null;

#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
private ?\DateTimeImmutable $validiteFin = null;

#[ORM\Column(length: 20)]
private ?string $status = 'en_attente';

    public function __construct()
    {
        $this->offrePieces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroOffre(): ?string
    {
        return $this->numeroOffre;
    }

    public function setNumeroOffre(string $numeroOffre): static
    {
        $this->numeroOffre = $numeroOffre;

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


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDemande(): ?demande
    {
        return $this->demande;
    }

    public function setDemande(?demande $demande): static
    {
        $this->demande = $demande;

        return $this;
    }

    public function getUser(): ?users
    {
        return $this->user;
    }

    public function setUser(?users $user): static
    {
        $this->user = $user;

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
            $offrePiece->setOffre($this);
        }

        return $this;
    }

    public function removeOffrePiece(OffrePiece $offrePiece): static
    {
        if ($this->offrePieces->removeElement($offrePiece)) {
            // set the owning side to null (unless already changed)
            if ($offrePiece->getOffre() === $this) {
                $offrePiece->setOffre(null);
            }
        }

        return $this;
    }

    public function getValiditeDebut(): ?\DateTimeImmutable
    {
        return $this->validiteDebut;
    }

    public function setValiditeDebut(\DateTimeImmutable $validiteDebut): static
    {
        $this->validiteDebut = $validiteDebut;

        return $this;
    }

    public function getValiditeFin(): ?\DateTimeImmutable
    {
        return $this->validiteFin;
    }

    public function setValiditeFin(\DateTimeImmutable $validiteFin): static
    {
        $this->validiteFin = $validiteFin;

        return $this;
    }

    public function getStatus(): ?string
{
    return $this->status;
}

public function setStatus(?string $status): static
{
    $this->status = $status;
    return $this;
}

// src/Entity/Offre.php

public function getOffrePieceByPiece($piece): ?OffrePiece
{
    foreach ($this->offrePieces as $offrePiece) {
        if ($offrePiece->getPiece() === $piece) {
            return $offrePiece;
        }
    }
    return null;
}


}
