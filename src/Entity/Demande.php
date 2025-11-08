<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(length: 255)]
    private ?string $chassis = null;

    #[ORM\Column(length: 255)]
    private ?string $energie = null;

    #[ORM\Column(length: 255)]
    private ?string $etatmoteur = null;

    #[ORM\Column(length: 255)]
    private ?string $photocartegrise = null;

    /**
     * @var Collection<int, Pieces>
     */
    #[ORM\OneToMany(targetEntity: Pieces::class, mappedBy: 'demande')]
    private Collection $Pieces;

    #[ORM\Column]
    private ?int $vendeurneuf = null;

    #[ORM\Column]
    private ?int $vendeuroccasion = null;

    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $offreemail = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    private ?Users $offrecompte = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datecreate = null;

     #[ORM\Column(length: 50, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    /**
     * @var Collection<int, Offre>
     */
    #[ORM\OneToMany(targetEntity: Offre::class, mappedBy: 'demande')]
    private Collection $offres;

    public function __construct()
    {
        $this->Pieces = new ArrayCollection();
        $this->offres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getChassis(): ?string
    {
        return $this->chassis;
    }

    public function setChassis(string $chassis): static
    {
        $this->chassis = $chassis;

        return $this;
    }

    public function getEnergie(): ?string
    {
        return $this->energie;
    }

    public function setEnergie(string $energie): static
    {
        $this->energie = $energie;

        return $this;
    }

    public function getEtatmoteur(): ?string
    {
        return $this->etatmoteur;
    }

    public function setEtatmoteur(string $etatmoteur): static
    {
        $this->etatmoteur = $etatmoteur;

        return $this;
    }

    public function getPhotocartegrise(): ?string
    {
        return $this->photocartegrise;
    }

    public function setPhotocartegrise(string $photocartegrise): static
    {
        $this->photocartegrise = $photocartegrise;

        return $this;
    }

    /**
     * @return Collection<int, Pieces>
     */
    public function getPieces(): Collection
    {
        return $this->Pieces;
    }

    public function addPiece(Pieces $piece): static
    {
        if (!$this->Pieces->contains($piece)) {
            $this->Pieces->add($piece);
            $piece->setDemande($this);
        }

        return $this;
    }

    public function removePiece(Pieces $piece): static
    {
        if ($this->Pieces->removeElement($piece)) {
            // set the owning side to null (unless already changed)
            if ($piece->getDemande() === $this) {
                $piece->setDemande(null);
            }
        }

        return $this;
    }

    public function getVendeurneuf(): ?int
    {
        return $this->vendeurneuf;
    }

    public function setVendeurneuf(int $vendeurneuf): static
    {
        $this->vendeurneuf = $vendeurneuf;

        return $this;
    }

    public function getVendeuroccasion(): ?int
    {
        return $this->vendeuroccasion;
    }

    public function setVendeuroccasion(int $vendeuroccasion): static
    {
        $this->vendeuroccasion = $vendeuroccasion;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function getOffreemail(): ?string
    {
        return $this->offreemail;
    }

    public function setOffreemail(?string $offreemail): static
    {
        $this->offreemail = $offreemail;

        return $this;
    }

    public function getOffrecompte(): ?Users
    {
        return $this->offrecompte;
    }

    public function setOffrecompte(?Users $offrecompte): static
    {
        $this->offrecompte = $offrecompte;

        return $this;
    }

    public function getDatecreate(): ?\DateTimeImmutable
    {
        return $this->datecreate;
    }

    public function setDatecreate(\DateTimeImmutable $datecreate): static
    {
        $this->datecreate = $datecreate;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): static
    {
        if (!$this->offres->contains($offre)) {
            $this->offres->add($offre);
            $offre->setDemande($this);
        }

        return $this;
    }

    public function removeOffre(Offre $offre): static
    {
        if ($this->offres->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getDemande() === $this) {
                $offre->setDemande(null);
            }
        }

        return $this;
    }
}
