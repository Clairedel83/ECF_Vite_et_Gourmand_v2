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

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_total = null;

    #[ORM\Column]
    private ?int $nbre_pers = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_livraison = null;

    #[ORM\Column]
    private int $statut = 1;

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

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    // ne modifie pas la commande en cas de suppression de menu
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Menu $menu = null;

    #[ORM\Column(length: 255)]
    private ?string $menu_nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $menu_composition = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_per_pers = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant_reduction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_sous_total = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_materiel = null;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'commande')]
    private Collection $avis;

    public function __construct()
    {
        $this->commandeHistorique = new ArrayCollection();
        $this->avis = new ArrayCollection();
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

    public function getPrixTotal(): ?string
    {
        return $this->prix_total;
    }

    public function setPrixTotal(string $prix_total): static
    {
        $this->prix_total = $prix_total;

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

    public function getPrixLivraison(): ?string
    {
        return $this->prix_livraison;
    }

    public function setPrixLivraison(string $prix_livraison): static
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

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    public function getMenuNom(): ?string
    {
        return $this->menu_nom;
    }

    public function setMenuNom(string $menu_nom): static
    {
        $this->menu_nom = $menu_nom;

        return $this;
    }

    public function getMenuComposition(): ?string
    {
        return $this->menu_composition;
    }

    public function setMenuComposition(string $menu_composition): static
    {
        $this->menu_composition = $menu_composition;

        return $this;
    }

    public function getPrixPerPers(): ?string
    {
        return $this->prix_per_pers;
    }

    public function setPrixPerPers(string $prix_per_pers): static
    {
        $this->prix_per_pers = $prix_per_pers;

        return $this;
    }

    public function getMontantReduction(): ?string
    {
        return $this->montant_reduction;
    }

    public function setMontantReduction(string $montant_reduction): static
    {
        $this->montant_reduction = $montant_reduction;

        return $this;
    }

    public function getPrixSousTotal(): ?string
    {
        return $this->prix_sous_total;
    }

    public function setPrixSousTotal(string $prix_sous_total): static
    {
        $this->prix_sous_total = $prix_sous_total;

        return $this;
    }

    public function getPrixMateriel(): ?string
    {
        return $this->prix_materiel;
    }

    public function setPrixMateriel(string $prix_materiel): static
    {
        $this->prix_materiel = $prix_materiel;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setCommande($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getCommande() === $this) {
                $avi->setCommande(null);
            }
        }

        return $this;
    }
}
