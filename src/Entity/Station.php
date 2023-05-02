<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nomS = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $adresseS = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $ligneS = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $horaireS = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $equipementS = null;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    #[Assert\NotBlank]
    private ?Circuit $circuit = null;

    #[ORM\Column]
    private ?float $pos1 = null;

    #[ORM\Column]
    private ?float $pos2 = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $commentaireS = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomS(): ?string
    {
        return $this->nomS;
    }

    public function setNomS(string $nomS): self
    {
        $this->nomS = $nomS;

        return $this;
    }

    public function getAdresseS(): ?string
    {
        return $this->adresseS;
    }

    public function setAdresseS(string $adresseS): self
    {
        $this->adresseS = $adresseS;

        return $this;
    }

    public function getLigneS(): ?string
    {
        return $this->ligneS;
    }

    public function setLigneS(string $ligneS): self
    {
        $this->ligneS = $ligneS;

        return $this;
    }

    public function getHoraireS(): ?\DateTimeInterface
    {
        return $this->horaireS;
    }

    public function setHoraireS(\DateTimeInterface $horaireS): self
    {
        $this->horaireS = $horaireS;

        return $this;
    }

    public function getEquipementS(): ?string
    {
        return $this->equipementS;
    }

    public function setEquipementS(string $equipementS): self
    {
        $this->equipementS = $equipementS;

        return $this;
    }

    public function getCircuit(): ?Circuit
    {
        return $this->circuit;
    }

    public function setCircuit(?Circuit $circuit): self
    {
        $this->circuit = $circuit;

        return $this;
    }

    public function getPos1(): ?float
    {
        return $this->pos1;
    }

    public function setPos1(float $pos1): self
    {
        $this->pos1 = $pos1;

        return $this;
    }

    public function getPos2(): ?float
    {
        return $this->pos2;
    }

    public function setPos2(float $pos2): self
    {
        $this->pos2 = $pos2;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCommentaireS(): ?string
    {
        return $this->commentaireS;
    }

    public function setCommentaireS(string $commentaireS): self
    {
        $this->commentaireS = $commentaireS;

        return $this;
    }
}
