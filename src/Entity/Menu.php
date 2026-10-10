<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $nbre_min = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix_per_pers = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Entree $entree=null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Plat $plat=null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Dessert $dessert=null;

    #[ORM\ManyToMany(targetEntity: Regime::class, inversedBy: 'menus')]
    private Collection $regimes;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Condition>
     */
    #[ORM\ManyToMany(targetEntity: Condition::class, inversedBy: 'menus')]
    private Collection $conditions;

    #[ORM\Column(length: 255)]
    private ?string $illustration = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Regex(
    pattern: '/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])$/',
    message: 'La date doit respecter le format JJ-MM.'
    )]
    private ?string $disponibilite_debut = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Regex(
    pattern: '/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])$/',
    message: 'La date doit respecter le format JJ-MM.'
    )]
    private ?string $disponibilite_fin = null;

    // ajoute des contraintes lors des choix de date de disponibilite
    #[Assert\Callback]
    public function validateDisponibilite(ExecutionContextInterface $context): void
    {
        // oblige l'employé à compléter la date de début ET date de fin de disponibilité du menu
        if (
            ($this->disponibilite_debut === null && $this->disponibilite_fin !== null)
            ||
            ($this->disponibilite_debut !== null && $this->disponibilite_fin === null)
        ) {
            $context->buildViolation('Les deux dates de disponibilité doivent être renseignées.')
                ->atPath('disponibilite_debut')
                ->addViolation();
        }

        // vérifie que la date existe
        foreach (['disponibilite_debut', 'disponibilite_fin'] as $champ) {

        $date = $this->$champ;

        // Si le champ est vide, on ne vérifie pas la date
        if ($date === null) {
            continue;
        }

        // Vérifie d'abord le format JJ-MM
        if (!preg_match('/^\d{2}-\d{2}$/', $date)) {
            continue;
        }

        // Sépare le jour et le mois
        [$jour, $mois] = explode('-', $date);

        // Vérifie si la date existe
        // année 2024 choisie car bissextile : 29 février existe
        if (!checkdate((int) $mois, (int) $jour, 2024)) {
            $context->buildViolation('Cette date n’existe pas.')
                ->atPath($champ)
                ->addViolation();
        }
    }
    }


    public function __construct()
    {
        $this->regimes = new ArrayCollection();
        $this->conditions = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNbreMin(): ?int
    {
        return $this->nbre_min;
    }

    public function setNbreMin(int $nbre_min): static
    {
        $this->nbre_min = $nbre_min;

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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getEntree(): ?Entree
    {
        return $this->entree;
    }

    public function setEntree(?Entree $entree): static
    {
        $this->entree = $entree;

        return $this;
    }


    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): static
    {
        $this->plat = $plat;

        return $this;
    }

    public function getDessert(): ?Dessert
    {
        return $this->dessert;
    }

    public function setDessert(?Dessert $dessert): static
    {
        $this->dessert = $dessert;

        return $this;
    }

    public function getRegimes(): Collection
    {
        return $this->regimes;
    }

    public function addRegime(Regime $regime): static
    {
        if (!$this->regimes->contains($regime)) {
            $this->regimes->add($regime);
        }

        return $this;
    }

    public function removeRegime(Regime $regime): static
    {
        $this->regimes->removeElement($regime);

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection<int, Condition>
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    public function addConditions(Condition $conditions): static
    {
        if (!$this->conditions->contains($conditions)) {
            $this->conditions->add($conditions);
        }

        return $this;
    }

    public function removeConditions(Condition $conditions): static
    {
        $this->conditions->removeElement($conditions);

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(string $illustration): static
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getDisponibiliteDebut(): ?string
    {
        return $this->disponibilite_debut;
    }

    public function setDisponibiliteDebut(?string $disponibilite_debut): static
    {
        $this->disponibilite_debut = $disponibilite_debut;

        return $this;
    }

    public function getDisponibiliteFin(): ?string
    {
        return $this->disponibilite_fin;
    }

    public function setDisponibiliteFin(?string $disponibilite_fin): static
    {
        $this->disponibilite_fin = $disponibilite_fin;

        return $this;
    }
}
