<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EspectrosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EspectrosRepository::class)]
#[ORM\Table(name: "espectros")]
class Espectros
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idespectros', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $nombre_evento = null;

    #[ORM\Column(length: 45)]
    private ?string $estacion = null;

    #[ORM\Column]
    private ?float $sa_max_cm_s2 = null;

    #[ORM\Column]
    private ?float $periodo_max_s = null;

    #[ORM\Column]
    private ?float $sa_02_cm_s2 = null;

    #[ORM\Column]
    private ?float $sa_10_cm_s2 = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre_archivo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreEvento(): ?string
    {
        return $this->nombre_evento;
    }

    public function setNombreEvento(string $nombre_evento): static
    {
        $this->nombre_evento = $nombre_evento;

        return $this;
    }

    public function getNombreArchivo(): ?string
    {
        return $this->nombre_archivo;
    }

    public function setNombreArchivo(string $nombre_archivo): static
    {
        $this->nombre_archivo = $nombre_archivo;

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

    public function getSaMaxCmS2(): ?float
    {
        return $this->sa_max_cm_s2;
    }

    public function setSaMaxCmS2(float $sa_max_cm_s2): static
    {
        $this->sa_max_cm_s2 = $sa_max_cm_s2;

        return $this;
    }

    public function getPeriodoMaxS(): ?float
    {
        return $this->periodo_max_s;
    }

    public function setPeriodoMaxS(float $periodo_max_s): static
    {
        $this->periodo_max_s = $periodo_max_s;

        return $this;
    }

    public function getSa02CmS2(): ?float
    {
        return $this->sa_02_cm_s2;
    }

    public function setSa02CmS2(float $sa_02_cm_s2): static
    {
        $this->sa_02_cm_s2 = $sa_02_cm_s2;

        return $this;
    }

    public function getSa10CmS2(): ?float
    {
        return $this->sa_10_cm_s2;
    }

    public function setSa10CmS2(float $sa_10_cm_s2): static
    {
        $this->sa_10_cm_s2 = $sa_10_cm_s2;

        return $this;
    }
}
