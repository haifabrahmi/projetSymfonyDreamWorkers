<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_usr", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUsr;

     /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="idUser")
     */
    private $idPub = array();

   

  

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idPub = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdUsr(): ?int
    {
        return $this->idUsr;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getIdPub(): Collection
    {
        return $this->idPub;
    }

    public function addIdPub(Publication $idPub): self
    {
        if (!$this->idPub->contains($idPub)) {
            $this->idPub->add($idPub);
            $idPub->addIdUser($this);
        }

        return $this;
    }

    public function removeIdPub(Publication $idPub): self
    {
        if ($this->idPub->removeElement($idPub)) {
            $idPub->removeIdUser($this);
        }

        return $this;
    }
    
  
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getNom();
    }

}
