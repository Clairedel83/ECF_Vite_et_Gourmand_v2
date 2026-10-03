<?php

namespace App\Entity;

use App\Repository\CommandeHistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeHistoriqueRepository::class)]
class CommandeHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTime $date_modification = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_modification = null;

    #[ORM\ManyToOne(inversedBy: 'commandeHistorique')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTime $date_modification): static
    {
        $this->date_modification = $date_modification;

        return $this;
    }

    public function getHeureModification(): ?\DateTime
    {
        return $this->heure_modification;
    }

    public function setHeureModification(\DateTime $heure_modification): static
    {
        $this->heure_modification = $heure_modification;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }
}
