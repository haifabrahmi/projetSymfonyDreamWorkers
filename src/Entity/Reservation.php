<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_res = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_res = null;

    #[ORM\Column(length: 255)]
    private ?string $heure_res = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $nb_place = null;

    #[ORM\Column]
    private ?float $prix_totale = null;

    #[ORM\Column(length: 255)]
    private ?string $type_ticket = null;

    public function getId_res (): ?int
    {
        return $this->id_res;
    }

    public function getDateRes(): ?\DateTimeInterface
    {
        return $this->date_res;
    }

    public function setDateRes(\DateTimeInterface $date_res): self
    {
        $this->date_res = $date_res;

        return $this;
    }

    public function getHeureRes(): ?string
    {
        return $this->heure_res;
    }

    public function setHeureRes(string $heure_res): self
    {
        $this->heure_res = $heure_res;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nb_place;
    }

    public function setNbPlace(int $nb_place): self
    {
        $this->nb_place = $nb_place;

        return $this;
    }

    public function getPrixTotale(): ?float
        {
        return $this->prix_totale;
    }

    public function setPrixTotale(float $prix_totale): self
    {
        $this->prix_totale = $prix_totale;

        return $this;
    }

    public function getTypeTicket(): ?string
    {
        return $this->type_ticket;
    }

    public function setTypeTicket(string $type_ticket): self
    {
        $this->type_ticket = $type_ticket;

        return $this;
    }
    
}
