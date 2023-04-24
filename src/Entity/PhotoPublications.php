<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhotoPublications
 *
 * @ORM\Table(name="photo_publications", indexes={@ORM\Index(name="id_pub", columns={"id_pub"})})
 * @ORM\Entity
 */
class PhotoPublications
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_ph", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPh;

    /**
     * @var string
     *
     * @ORM\Column(name="lien", type="string", length=100, nullable=false)
     */
    private $lien;

    /**
     * @var \Publication
     *
     * @ORM\ManyToOne(targetEntity="Publication")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pub", referencedColumnName="id_pub")
     * })
     */
    private $idPub;

    public function getIdPh(): ?int
    {
        return $this->idPh;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getIdPub(): ?Publication
    {
        return $this->idPub;
    }

    public function setIdPub(?Publication $idPub): self
    {
        $this->idPub = $idPub;

        return $this;
    }


}
