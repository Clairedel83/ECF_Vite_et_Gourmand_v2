<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_commande = null;

    #[ORM\Column]
    private ?\DateTime $date_commande = null;

    #[ORM\Column]
    private ?\DateTime $date_prestation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_livraison = null;

    #[ORM\Column(length: 255)]
    private ?string $prix_menu = null;

    #[ORM\Column]
    private ?int $nbre_pers = null;

    #[ORM\Column]
    private ?float $prix_livraison = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?bool $pret_materiel = null;

    #[ORM\Column]
    private ?bool $restitution_materiel = null;

    /**
     * @var Collection<int, CommandeHistorique>
     */
    #[ORM\OneToMany(targetEntity: CommandeHistorique::class, mappedBy: 'commande')]
    private Collection $commandeHistorique;

    public function __construct()
    {
        $this->commandeHistorique = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?int
    {
        return $this->numero_commande;
    }

    public function setNumeroCommande(int $numero_commande): static
    {
        $this->numero_commande = $numero_commande;

        return $this;
    }

    public function getDateCommande(): ?\DateTime
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTime $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getDatePrestation(): ?\DateTime
    {
        return $this->date_prestation;
    }

    public function setDatePrestation(\DateTime $date_prestation): static
    {
        $this->date_prestation = $date_prestation;

        return $this;
    }

    public function getHeureLivraison(): ?\DateTime
    {
        return $this->heure_livraison;
    }

    public function setHeureLivraison(\DateTime $heure_livraison): static
    {
        $this->heure_livraison = $heure_livraison;

        return $this;
    }

    public function getPrixMenu(): ?string
    {
        return $this->prix_menu;
    }

    public function setPrixMenu(string $prix_menu): static
    {
        $this->prix_menu = $prix_menu;

        return $this;
    }

    public function getNbrePers(): ?int
    {
        return $this->nbre_pers;
    }

    public function setNbrePers(int $nbre_pers): static
    {
        $this->nbre_pers = $nbre_pers;

        return $this;
    }

    public function getPrixLivraison(): ?float
    {
        return $this->prix_livraison;
    }

    public function setPrixLivraison(float $prix_livraison): static
    {
        $this->prix_livraison = $prix_livraison;

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

    public function isPretMateriel(): ?bool
    {
        return $this->pret_materiel;
    }

    public function setPretMateriel(bool $pret_materiel): static
    {
        $this->pret_materiel = $pret_materiel;

        return $this;
    }

    public function isRestitutionMateriel(): ?bool
    {
        return $this->restitution_materiel;
    }

    public function setRestitutionMateriel(bool $restitution_materiel): static
    {
        $this->restitution_materiel = $restitution_materiel;

        return $this;
    }

    /**
     * @return Collection<int, CommandeHistorique>
     */
    public function getCommandeHistorique(): Collection
    {
        return $this->commandeHistorique;
    }

    public function addCommandeHistorique(CommandeHistorique $commandeHistorique): static
    {
        if (!$this->commandeHistorique->contains($commandeHistorique)) {
            $this->commandeHistorique->add($commandeHistorique);
            $commandeHistorique->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeHistorique(CommandeHistorique $commandeHistorique): static
    {
        if ($this->commandeHistorique->removeElement($commandeHistorique)) {
            // set the owning side to null (unless already changed)
            if ($commandeHistorique->getCommande() === $this) {
                $commandeHistorique->setCommande(null);
            }
        }

        return $this;
    }
}
