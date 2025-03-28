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
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idpga = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $nombre = null;

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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }
}
