<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_ticket = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $type_ticket = null;

    public function getId_ticket(): ?int
    {
        return $this->id_ticket;
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
