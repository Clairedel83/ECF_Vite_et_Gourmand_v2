<?php

namespace App\Entity;

use App\Repository\HorairesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HorairesRepository::class)]
class Horaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $matinDebut = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $matinFin = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $apresMidiDebut = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $apresMidiFin = null;

    #[ORM\Column]
    private ?int $jour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatinDebut(): ?\DateTimeImmutable
    {
        return $this->matinDebut;
    }

    public function setMatinDebut(?\DateTimeImmutable $matin_debut): static
    {
        $this->matinDebut = $matin_debut;

        return $this;
    }

    public function getMatinFin(): ?\DateTimeImmutable
    {
        return $this->matinFin;
    }

    public function setMatinFin(?\DateTimeImmutable $matinFin): static
    {
        $this->matinFin = $matinFin;

        return $this;
    }

    public function getApresMidiDebut(): ?\DateTimeImmutable
    {
        return $this->apresMidiDebut;
    }

    public function setApresMidiDebut(?\DateTimeImmutable $apresMidiDebut): static
    {
        $this->apresMidiDebut = $apresMidiDebut;

        return $this;
    }

    public function getApresMidiFin(): ?\DateTimeImmutable
    {
        return $this->apresMidiFin;
    }

    public function setApresMidiFin(?\DateTimeImmutable $apresMidiFin): static
    {
        $this->apresMidiFin = $apresMidiFin;

        return $this;
    }

    public function getJour(): ?int
    {
        return $this->jour;
    }

    public function setJour(int $jour): static
    {
        $this->jour = $jour;

        return $this;
    }
}
