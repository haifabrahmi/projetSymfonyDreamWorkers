<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Circuit
 *
 * @ORM\Table(name="circuit")
 * @ORM\Entity
 */
class Circuit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_c", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idC;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_c", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $nomC;

    /**
     * @var int
     *
     * @ORM\Column(name="liste_c", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $listeC;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrbus_c", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=5)
     */
    private $nbrbusC;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horaire_c", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    private $horaireC;

    /**
     * @var string
     *
     * @ORM\Column(name="distance_c", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $distanceC;

    public function getIdC(): ?int
    {
        return $this->idC;
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

    public function __toString()
    {
        return (string) $this->nomC;
    }


}
