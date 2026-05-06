<?php

namespace App\Entity;

use App\Repository\HistoricoSismosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoricoSismosRepository::class)]
class HistoricoSismos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'idEvento',length: 100)]
    private ?string $idEvento = null;

    #[ORM\Column(name: 'fechaEvento',type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaEvento = null;

    #[ORM\Column(name: 'latitudEvento')]
    private ?float $latitudEvento = null;

    #[ORM\Column(name: 'longitudEvento')]
    private ?float $longitudEvento = null;

    #[ORM\Column(name: 'magnitudEvento')]
    private ?float $magnitudEvento = null;

    #[ORM\Column(name: 'aceleracionEvento',)]
    private ?float $aceleracionEvento = null;

    #[ORM\Column(name: 'lugarAceleracion',length: 30)]
    private ?string $lugarAceleracion = null;

    #[ORM\Column(name: 'profundidadEvento',)]
    private ?float $profundidadEvento = null;

    #[ORM\Column(name: 'informe',)]
    private ?float $informe = null;

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

    public function getAceleracionEvento(): ?float
    {
        return $this->aceleracionEvento;
    }

    public function setAceleracionEvento(float $aceleracionEvento): static
    {
        $this->aceleracionEvento = $aceleracionEvento;

        return $this;
    }

    public function getProfundidadEvento(): ?float
    {
        return $this->profundidadEvento;
    }

    public function setProfundidadEvento(float $profundidadEvento): static
    {
        $this->profundidadEvento = $profundidadEvento;

        return $this;
    }

    public function getInforme(): ?float
    {
        return $this->informe;
    }

    public function setInforme(float $informe): static
    {
        $this->informe = $informe;

        return $this;
    }

    public function getLugarAceleracion(): ?string
    {
        return $this->lugarAceleracion;
    }

    public function setLugarAceleracion(string $lugarAceleracion): static
    {
        $this->lugarAceleracion = $lugarAceleracion;

        return $this;
    }
}
