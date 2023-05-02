<?php

namespace App\Entity;

use App\Repository\CircuitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CircuitRepository::class)]
class Circuit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nomC = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $listeC = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $nbrbusC = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $horaireC = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $distanceC = null;

    #[ORM\OneToMany(mappedBy: 'circuit', targetEntity: Station::class)]
    private Collection $stations;

    public function __construct()
    {
        $this->stations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomC(): ?string
    {
        return $this->nomC;
    }

    public function setNomC(string $nomC): self
    {
        $this->nomC = $nomC;

        return $this;
    }

    public function getListeC(): ?int
    {
        return $this->listeC;
    }

    public function setListeC(int $listeC): self
    {
        $this->listeC = $listeC;

        return $this;
    }

    public function getNbrbusC(): ?int
    {
        return $this->nbrbusC;
    }

    public function setNbrbusC(int $nbrbusC): self
    {
        $this->nbrbusC = $nbrbusC;

        return $this;
    }

    public function getHoraireC(): ?\DateTimeInterface
    {
        return $this->horaireC;
    }

    public function setHoraireC(\DateTimeInterface $horaireC): self
    {
        $this->horaireC = $horaireC;

        return $this;
    }

    public function getDistanceC(): ?string
    {
        return $this->distanceC;
    }

    public function setDistanceC(string $distanceC): self
    {
        $this->distanceC = $distanceC;

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): self
    {
        if (!$this->stations->contains($station)) {
            $this->stations->add($station);
            $station->setId($this);
        }

        return $this;
    }

    public function removeStation(Station $station): self
    {
        if ($this->stations->removeElement($station)) {
            // set the owning side to null (unless already changed)
            if ($station->getId() === $this) {
                $station->setId(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->nomC;
    }
}
