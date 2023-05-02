<?php

namespace App\Entity;

use App\Entity\Bus;
use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_m = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_entretien = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Bus::class)]
    #[ORM\JoinColumn(name: "id_bus", referencedColumnName: "id_bus")]
    private ?Bus $bus = null;

    public function getId_m(): ?int
    {
        return $this->id_m;
    }

    public function getDateEntretien(): ?\DateTimeInterface
    {
        return $this->date_entretien;
    }

    public function setDateEntretien(\DateTimeInterface $date_entretien): self
    {
        $this->date_entretien = $date_entretien;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBus(): ?Bus
    {
        return $this->bus;
    }

    public function setBus(?Bus $bus): self
    {
        $this->bus = $bus;

        return $this;
    }

	
}
