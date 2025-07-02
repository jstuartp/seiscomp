<?php

namespace App\Entity;

use App\Repository\TodosSismosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodosSismosRepository::class)]
class TodosSismos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $idEvento = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaEvento = null;

    #[ORM\Column]
    private ?float $latitudEvento = null;

    #[ORM\Column]
    private ?float $longitudEvento = null;

    #[ORM\Column]
    private ?float $magnitudEvento = null;

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

    public function getFechaEvento(): ?\DateTimeInterface
    {
        return $this->fechaEvento;
    }

    public function setFechaEvento(\DateTimeInterface $fechaEvento): static
    {
        $this->fechaEvento = $fechaEvento;

        return $this;
    }

    public function getLatitudEvento(): ?float
    {
        return $this->latitudEvento;
    }

    public function setLatitudEvento(float $latitudEvento): static
    {
        $this->latitudEvento = $latitudEvento;

        return $this;
    }

    public function getLongitudEvento(): ?float
    {
        return $this->longitudEvento;
    }

    public function setLongitudEvento(float $longitudEvento): static
    {
        $this->longitudEvento = $longitudEvento;

        return $this;
    }

    public function getMagnitudEvento(): ?float
    {
        return $this->magnitudEvento;
    }

    public function setMagnitudEvento(float $magnitudEvento): static
    {
        $this->magnitudEvento = $magnitudEvento;

        return $this;
    }
}
