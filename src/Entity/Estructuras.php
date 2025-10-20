<?php

namespace App\Entity;

use App\Repository\EstructurasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstructurasRepository::class)]
class Estructuras
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre_edificio = null;

    #[ORM\Column]
    private ?float $lat_edificio = null;

    #[ORM\Column]
    private ?float $long_edificio = null;

    #[ORM\Column(nullable: true)]
    private ?int $pisos = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $espectros = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $estaciones_edificio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNombreEdificio(): ?string
    {
        return $this->nombre_edificio;
    }

    public function setNombreEdificio(string $nombre_edificio): static
    {
        $this->nombre_edificio = $nombre_edificio;

        return $this;
    }

    public function getLatEdificio(): ?float
    {
        return $this->lat_edificio;
    }

    public function setLatEdificio(float $lat_edificio): static
    {
        $this->lat_edificio = $lat_edificio;

        return $this;
    }

    public function getLongEdificio(): ?float
    {
        return $this->long_edificio;
    }

    public function setLongEdificio(float $long_edificio): static
    {
        $this->long_edificio = $long_edificio;

        return $this;
    }

    public function getPisos(): ?int
    {
        return $this->pisos;
    }

    public function setPisos(?int $pisos): static
    {
        $this->pisos = $pisos;

        return $this;
    }

    public function getEspectros(): ?string
    {
        return $this->espectros;
    }

    public function setEspectros(?string $espectros): static
    {
        $this->espectros = $espectros;

        return $this;
    }

    public function getEstacionesEdificio(): ?string
    {
        return $this->estaciones_edificio;
    }

    public function setEstacionesEdificio(?string $estaciones_edificio): static
    {
        $this->estaciones_edificio = $estaciones_edificio;

        return $this;
    }
}
