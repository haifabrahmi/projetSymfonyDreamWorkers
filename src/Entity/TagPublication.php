<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagPublication
 *
 * @ORM\Table(name="tag_publication", indexes={@ORM\Index(name="id_tag", columns={"id_tag"}), @ORM\Index(name="id_pub", columns={"id_pub"})})
 * @ORM\Entity
 */
class TagPublication
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Tag
     *
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tag", referencedColumnName="id_tag")
     * })
     */
    private $idTag;

    /**
     * @var \Publication
     *
     * @ORM\ManyToOne(targetEntity="Publication")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pub", referencedColumnName="id_pub")
     * })
     */
    private $idPub;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTag(): ?Tag
    {
        return $this->idTag;
    }

    public function setIdTag(?Tag $idTag): self
    {
        $this->idTag = $idTag;

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
