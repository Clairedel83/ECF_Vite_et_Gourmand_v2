<?php

namespace App\Entity;

use App\Repository\AllergeneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllergeneRepository::class)]
class Allergene
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\ManyToMany(targetEntity: Plat::class, mappedBy: 'allergene')]
    private Collection $plats;

    /**
     * @var Collection<int, Entree>
     */
    #[ORM\ManyToMany(targetEntity: Entree::class, mappedBy: 'allergene')]
    private Collection $entrees;

    /**
     * @var Collection<int, Dessert>
     */
    #[ORM\ManyToMany(targetEntity: Dessert::class, mappedBy: 'allergene')]
    private Collection $desserts;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
        $this->entrees = new ArrayCollection();
        $this->desserts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->addAllergene($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->plats->removeElement($plat)) {
            $plat->removeAllergene($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Entree>
     */
    public function getEntrees(): Collection
    {
        return $this->entrees;
    }

    public function addEntree(Entree $entree): static
    {
        if (!$this->entrees->contains($entree)) {
            $this->entrees->add($entree);
            $entree->addAllergene($this);
        }

        return $this;
    }

    public function removeEntree(Entree $entree): static
    {
        if ($this->entrees->removeElement($entree)) {
            $entree->removeAllergene($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Dessert>
     */
    public function getDesserts(): Collection
    {
        return $this->desserts;
    }

    public function addDessert(Dessert $dessert): static
    {
        if (!$this->desserts->contains($dessert)) {
            $this->desserts->add($dessert);
            $dessert->addAllergene($this);
        }

        return $this;
    }

    public function removeDessert(Dessert $dessert): static
    {
        if ($this->desserts->removeElement($dessert)) {
            $dessert->removeAllergene($this);
        }

        return $this;
    }
}
