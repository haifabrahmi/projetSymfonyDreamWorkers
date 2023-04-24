<?php

namespace App\Entity;

use App\Repository\BusRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use \EasyCorp\Bundle\EasyRatingBundle\Model\RateableTrait;
use EasyCorp\Bundle\EasyRatingBundle\Model\RaterInterface;
use EasyCorp\Bundle\EasyRatingBundle\Model\Rate;

#[ORM\Entity(repositoryClass: BusRepository::class)]
class Bus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_bus = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column]
    private ?int $numero_de_plaque = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?DateTimeInterface $date_depart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?DateTimeInterface $date_arrive = null;

    #[ORM\Column(length: 255)]
    private ?string $destination = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    
    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    public function getRating(): ?int
    {
        return $this->rating;
    }

   

    // ...

    
    #[ORM\OneToMany(mappedBy: "bus", targetEntity: Maintenance::class)]
    private $maintenances;

    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
    }


    public function addMaintenance(Maintenance $maintenance): self
    {
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances[] = $maintenance;
            $maintenance->setBus($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): self
    {
        if ($this->maintenances->removeElement($maintenance)) {
            if ($maintenance->getBus() === $this) {
                $maintenance->setBus(null);
            }
        }

        return $this;
    }

    public function getMaintenances(): Collection
    {
        return $this->maintenances;
    }

    public function getId_bus(): ?int
    {
        return $this->id_bus;
    }
    public function __toString()
    {
        
       
         return $this->id_bus;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    public function getNumeroDePlaque(): ?int
    {
        return $this->numero_de_plaque;
    }

    public function setNumeroDePlaque(?int $numero_de_plaque): self
    {
        $this->numero_de_plaque = $numero_de_plaque;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getDateDepart(): ?DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(?DateTimeInterface $date_depart): self
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getDateArrive(): ?DateTimeInterface
    {
        return $this->date_arrive;
    }

    public function setDateArrive(?DateTimeInterface $date_arrive): self
    {
        $this->date_arrive = $date_arrive;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }


    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

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

    public function getFormattedDateDepart(): ?string
    {
        $date_depart = $this->getDateDepart();
        if (!$date_depart) {
            return null;
        }

        return $date_depart->format('Y-m-d H:i:s');
    }
    public function getFormattedDateArrive(): ?string
    {
        $date_arrive= $this->getDateArrive();
        if (!$date_arrive) {
            return null;
        }

        return $date_arrive->format('Y-m-d H:i:s');
    }

	/**
	 * @return int|null
	 */
	public function getNumero_de_plaque(): ?int {
		return $this->numero_de_plaque;
	}
	
	/**
	 * @param int|null $numero_de_plaque 
	 * @return self
	 */
	public function setNumero_de_plaque(?int $numero_de_plaque): self {
		$this->numero_de_plaque = $numero_de_plaque;
		return $this;
	}
}
