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

    #[ORM\Column(length: 30)]
    private ?string $numero_commande = null;

    #[ORM\Column]
    private ?\DateTime $date_commande = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_commande = null;

    #[ORM\Column]
    private ?\DateTime $date_livraison = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_livraison = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_livraison = null;

    #[ORM\Column(length: 255)]
    private ?string $prix_menu = null;

    #[ORM\Column]
    private ?int $nbre_pers = null;

    #[ORM\Column]
    private ?float $prix_livraison = null;

    #[ORM\Column]
    private ?int $statut = null;

    #[ORM\Column]
    private ?bool $pret_materiel = null;

    #[ORM\Column]
    private ?bool $restitution_materiel = null;

    /**
     * @var Collection<int, CommandeHistorique>
     */
    #[ORM\OneToMany(targetEntity: CommandeHistorique::class, mappedBy: 'commande')]
    private Collection $commandeHistorique;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->commandeHistorique = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numero_commande;
    }

    public function setNumeroCommande(string $numero_commande): static
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

    public function getDateLivraison(): ?\DateTime
    {
        return $this->date_livraison;
    }

    public function setDateLivraison(\DateTime $date_livraison): static
    {
        $this->date_livraison = $date_livraison;

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

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): static
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

    public function setRestitutionMateriel(?bool $restitution_materiel): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    public function setAdresseLivraison(string $adresse_livraison): static
    {
        $this->adresse_livraison = $adresse_livraison;

        return $this;
    }

    public function getHeureCommande(): ?\DateTime
    {
        return $this->heure_commande;
    }

    public function setHeureCommande(\DateTime $heure_commande): static
    {
        $this->heure_commande = $heure_commande;

        return $this;
    }
}
