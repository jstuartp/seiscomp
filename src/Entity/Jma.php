<?php

namespace App\Entity;

use App\Repository\JmaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JmaRepository::class)]
class Jma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $idEvento = null;

    #[ORM\Column(length: 10)]
    private ?string $estacion = null;

    #[ORM\Column(nullable: true)]
    private ?float $threshold_a0 = null;

    #[ORM\Column(nullable: true)]
    private ?float $continuos = null;

    #[ORM\Column(nullable: true)]
    private ?float $truncated = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $jma = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdEvento(): ?string
    {
        return $this->idEvento;
    }

    public function setIdEvento(string $idEvento): static
    {
        $this->idEvento = $idEvento;

        return $this;
    }

    public function getEstacion(): ?string
    {
        return $this->estacion;
    }

    public function setEstacion(string $estacion): static
    {
        $this->estacion = $estacion;

        return $this;
    }

    public function getThresholdA0(): ?float
    {
        return $this->threshold_a0;
    }

    public function setThresholdA0(?float $threshold_a0): static
    {
        $this->threshold_a0 = $threshold_a0;

        return $this;
    }

    public function getContinuos(): ?float
    {
        return $this->continuos;
    }

    public function setContinuos(?float $continuos): static
    {
        $this->continuos = $continuos;

        return $this;
    }

    public function getTruncated(): ?float
    {
        return $this->truncated;
    }

    public function setTruncated(?float $truncated): static
    {
        $this->truncated = $truncated;

        return $this;
    }

    public function getJma(): ?string
    {
        return $this->jma;
    }

    public function setJma(?string $jma): static
    {
        $this->jma = $jma;

        return $this;
    }
}
