<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Station
 *
 * @ORM\Table(name="station")
 * @ORM\Entity(repositoryClass="App\Repository\StationRepository")
 */
class Station
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_s", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idS;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_s", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $nomS;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_s", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $adresseS;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne_s", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $ligneS;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horaire_s", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    private $horaireS;

    /**
     * @var string
     *
     * @ORM\Column(name="equipement_s", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $equipementS;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_s", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $commentaireS;

    /**
     * @var \Circuit
     *
     * @ORM\ManyToOne(targetEntity="Circuit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idC", referencedColumnName="id_c")
     * })
     * @Assert\NotBlank()
     */
    private $idC;

    public function getIdS(): ?int
    {
        return $this->idS;
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

    public function getCommentaireS(): ?string
    {
        return $this->commentaireS;
    }

    public function setCommentaireS(string $commentaireS): self
    {
        $this->commentaireS = $commentaireS;

        return $this;
    }

    public function getIdC(): ?Circuit
    {
        return $this->idC;
    }

    public function setIdC(?Circuit $idC): self
    {
        $this->idC = $idC;

        return $this;
    }


}
