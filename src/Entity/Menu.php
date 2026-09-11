<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use BcMath\Number;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @var Collection<int, Entree>
     */
    #[ORM\ManyToMany(targetEntity: Entree::class, inversedBy: 'menus')]
    private Collection $entree;

    /**
     * @var Collection<int, PLat>
     */
    #[ORM\ManyToMany(targetEntity: PLat::class, inversedBy: 'menus')]
    private Collection $plat;

    /**
     * @var Collection<int, Dessert>
     */
    #[ORM\ManyToMany(targetEntity: Dessert::class, inversedBy: 'menus')]
    private Collection $dessert;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Regime $regime = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Condition>
     */
    #[ORM\ManyToMany(targetEntity: Condition::class, inversedBy: 'menus')]
    private Collection $condition_stockage;

    public function __construct()
    {
        $this->entree = new ArrayCollection();
        $this->plat = new ArrayCollection();
        $this->dessert = new ArrayCollection();
        $this->condition_stockage = new ArrayCollection();
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

    public function getNbreMin(): ?Number
    {
        return $this->nbre_min;
    }

    public function setNbreMin(int $nbre_min): static
    {
        $this->nbre_min = $nbre_min;

        return $this;
    }

    public function getPrixPerPers(): ?float
    {
        return $this->prix_per_pers;
    }

    public function setPrixPerPers(float $prix_per_pers): static
    {
        $this->prix_per_pers = $prix_per_pers;

        return $this;
    }

    public function getStock(): ?Number
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, Entree>
     */
    public function getEntree(): Collection
    {
        return $this->entree;
    }

    public function addEntree(Entree $entree): static
    {
        if (!$this->entree->contains($entree)) {
            $this->entree->add($entree);
        }

        return $this;
    }

    public function removeEntree(Entree $entree): static
    {
        $this->entree->removeElement($entree);

        return $this;
    }

    /**
     * @return Collection<int, PLat>
     */
    public function getPlat(): Collection
    {
        return $this->plat;
    }

    public function addPlat(PLat $plat): static
    {
        if (!$this->plat->contains($plat)) {
            $this->plat->add($plat);
        }

        return $this;
    }

    public function removePlat(PLat $plat): static
    {
        $this->plat->removeElement($plat);

        return $this;
    }

    /**
     * @return Collection<int, Dessert>
     */
    public function getDessert(): Collection
    {
        return $this->dessert;
    }

    public function addDessert(Dessert $dessert): static
    {
        if (!$this->dessert->contains($dessert)) {
            $this->dessert->add($dessert);
        }

        return $this;
    }

    public function removeDessert(Dessert $dessert): static
    {
        $this->dessert->removeElement($dessert);

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): static
    {
        $this->regime = $regime;

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
    public function getConditionStockage(): Collection
    {
        return $this->condition_stockage;
    }

    public function addConditionStockage(Condition $conditionStockage): static
    {
        if (!$this->condition_stockage->contains($conditionStockage)) {
            $this->condition_stockage->add($conditionStockage);
        }

        return $this;
    }

    public function removeConditionStockage(Condition $conditionStockage): static
    {
        $this->condition_stockage->removeElement($conditionStockage);

        return $this;
    }
}
