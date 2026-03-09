<?php

namespace App\Entity;

use App\Repository\PgaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PgaRepository::class)]
#[ORM\Table(name: "Pga")]
class Pga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idpga', length: 255)]
    private ?int $id = null;


    #[ORM\Column(length: 30, nullable: true)]
    private ?string $fecha_evento = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $fecha_calculo = null;

    #[ORM\Column(length: 45)]
    private ?string $nombre_evento = null;

    #[ORM\Column]
    private ?int $tipo_estacion = null;

    #[ORM\Column(length: 45)]
    private ?string $estacion = null;

    #[ORM\Column]
    private ?float $latitud = null;

    #[ORM\Column]
    private ?float $longitud = null;

    #[ORM\Column]
    private ?float $hne_pga = null;

    #[ORM\Column]
    private ?float $hnn_pga = null;

    #[ORM\Column]
    private ?float $hnz_pga = null;

    #[ORM\Column]
    private ?float $maximo = null;

    #[ORM\Column(name: 'rutaWaveform', length: 255)]
    private ?string $rutaWaveform = null;

    #[ORM\Column]
    private ?float $min_filter = null;

    #[ORM\Column]
    private ?float $max_filter = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getFechaEvento(): ?string
    {
        return $this->fecha_evento;
    }

    public function setFechaEvento(?string $fecha_evento): static
    {
        $this->fecha_evento = $fecha_evento;

        return $this;
    }

    public function getFechaCalculo(): ?string
    {
        return $this->fecha_calculo;
    }

    public function setFechaCalculo(?string $fecha_calculo): static
    {
        $this->fecha_calculo = $fecha_calculo;

        return $this;
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

    public function getTipoEstacion(): ?int
    {
        return $this->tipo_estacion;
    }

    public function setTipoEstacion(int $tipo_estacion): static
    {
        $this->tipo_estacion = $tipo_estacion;

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

    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(float $latitud): static
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?float
    {
        return $this->longitud;
    }

    public function setLongitud(float $longitud): static
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function getHnePga(): ?float
    {
        return $this->hne_pga;
    }

    public function setHnePga(float $hne_pga): static
    {
        $this->hne_pga = $hne_pga;

        return $this;
    }

    public function getHnnPga(): ?float
    {
        return $this->hnn_pga;
    }

    public function setHnnPga(float $hnn_pga): static
    {
        $this->hnn_pga = $hnn_pga;

        return $this;
    }

    public function getHnzPga(): ?float
    {
        return $this->hnz_pga;
    }

    public function setHnzPga(float $hnz_pga): static
    {
        $this->hnz_pga = $hnz_pga;

        return $this;
    }

    public function getMaximo(): ?float
    {
        return $this->maximo;
    }

    public function setMaximo(float $maximo): static
    {
        $this->maximo = $maximo;

        return $this;
    }

    public function getRutaWaveform(): ?string
    {
        return $this->rutaWaveform;
    }

    public function setRutaWaveform(string $rutaWaveform): static
    {
        $this->rutaWaveform = $rutaWaveform;

        return $this;
    }

    public function getMinFilter(): ?float
    {
        return $this->min_filter;
    }

    public function setMinFilter(float $min_filter): static
    {
        $this->min_filter = $min_filter;

        return $this;
    }

    public function getMaxFilter(): ?float
    {
        return $this->max_filter;
    }

    public function setMaxFilter(float $max_filter): static
    {
        $this->max_filter = $max_filter;

        return $this;
    }
}
