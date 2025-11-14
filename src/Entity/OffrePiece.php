<?php

namespace App\Entity;

use App\Repository\OffrePieceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffrePieceRepository::class)]
class OffrePiece
{
   #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    private ?string $prix1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $marque1 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    private ?string $prix2 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $marque2 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, nullable: true)]
    private ?string $prix3 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $marque3 = null;

    #[ORM\ManyToOne(inversedBy: 'offrePieces')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Offre $offre = null;

    #[ORM\ManyToOne(inversedBy: 'offrePieces')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Pieces $piece = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix1(): ?string
    {
        return $this->prix1;
    }

    public function setPrix1(?string $prix1): static
    {
        $this->prix1 = $prix1;

        return $this;
    }

    public function getMarque1(): ?string
    {
        return $this->marque1;
    }

    public function setMarque1(?string $marque1): static
    {
        $this->marque1 = $marque1;

        return $this;
    }

    public function getPrix2(): ?string
    {
        return $this->prix2;
    }

    public function setPrix2(?string $prix2): static
    {
        $this->prix2 = $prix2;

        return $this;
    }

    public function getMarque2(): ?string
    {
        return $this->marque2;
    }

    public function setMarque2(?string $marque2): static
    {
        $this->marque2 = $marque2;

        return $this;
    }

    public function getPrix3(): ?string
    {
        return $this->prix3;
    }

    public function setPrix3(?string $prix3): static
    {
        $this->prix3 = $prix3;

        return $this;
    }

    public function getMarque3(): ?string
{
    return $this->marque3;   // ✔ correct
}

public function setMarque3(?string $marque3): static
{
    $this->marque3 = $marque3;  // ✔ correct

    return $this;
}


    public function getOffre(): ?offre
    {
        return $this->offre;
    }

    public function setOffre(?offre $offre): static
    {
        $this->offre = $offre;

        return $this;
    }

    public function getPiece(): ?pieces
    {
        return $this->piece;
    }

    public function setPiece(?pieces $piece): static
    {
        $this->piece = $piece;

        return $this;
    }
}
