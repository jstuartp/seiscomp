<?php

namespace App\Entity;

use App\Repository\PgaEstructurasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PgaEstructurasRepository::class)]
#[ORM\Table(name: "Pga_estructuras")]
class PgaEstructuras
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idpga = null;

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

    #[ORM\Column]
    private ?float $hne_pgv = null;

    #[ORM\Column]
    private ?float $hnn_pgv = null;

    #[ORM\Column]
    private ?float $hnz_pgv = null;

    #[ORM\Column]
    private ?float $hne_pgd = null;

    #[ORM\Column]
    private ?float $hnn_pgd = null;

    #[ORM\Column]
    private ?float $hnz_pgd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdpga(): ?int
    {
        return $this->idpga;
    }

    public function setIdpga(int $idpga): static
    {
        $this->idpga = $idpga;

        return $this;
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

    public function getHnePgv(): ?float
    {
        return $this->hne_pgv;
    }

    public function setHnePgv(float $hne_pgv): static
    {
        $this->hne_pgv = $hne_pgv;

        return $this;
    }

    public function getHnnPgv(): ?float
    {
        return $this->hnn_pgv;
    }

    public function setHnnPgv(float $hnn_pgv): static
    {
        $this->hnn_pgv = $hnn_pgv;

        return $this;
    }

    public function getHnzPgv(): ?float
    {
        return $this->hnz_pgv;
    }

    public function setHnzPgv(float $hnz_pgv): static
    {
        $this->hnz_pgv = $hnz_pgv;

        return $this;
    }

    public function getHnePgd(): ?float
    {
        return $this->hne_pgd;
    }

    public function setHnePgd(float $hne_pgd): static
    {
        $this->hne_pgd = $hne_pgd;

        return $this;
    }

    public function getHnnPgd(): ?float
    {
        return $this->hnn_pgd;
    }

    public function setHnnPgd(float $hnn_pgd): static
    {
        $this->hnn_pgd = $hnn_pgd;

        return $this;
    }

    public function getHnzPgd(): ?float
    {
        return $this->hnz_pgd;
    }

    public function setHnzPgd(float $hnz_pgd): static
    {
        $this->hnz_pgd = $hnz_pgd;

        return $this;
    }
}
