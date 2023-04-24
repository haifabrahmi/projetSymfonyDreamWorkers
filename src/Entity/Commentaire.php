<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="id_pub", columns={"id_pub"}), @ORM\Index(name="fk_user", columns={"id_user"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Commentaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_com", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCom;

    /**
     * @var string
     * @Assert\NotBlank(message="Le contenu ne peut pas être vide")
     * @Assert\Length(
     *     max = 500,
     *     min=5,
     *     maxMessage = "Le contenu ne peut pas dépasser {{ limit }} caractères",  
     *     minMessage = "Le contenu doit dépasser {{ limit }} caractères",   
     * )
     * @ORM\Column(name="suj_com", type="string", length=255, nullable=false)
     */
    private $sujCom;

 /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_com", type="date", length=50, nullable=false)
     */
    private $dateCom ;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->dateCom = new \DateTime();
    }
    /**
     * @var int
     *
     * @ORM\Column(name="nb_reaction", type="integer", nullable=false)
     */
    private $nbReaction = '0';

   /**
 * @var \Publication
 *
 * @ORM\ManyToOne(targetEntity="Publication", inversedBy="commentaires")
 * @ORM\JoinColumn(name="id_pub", referencedColumnName="id_pub")
 */
private $idPub;
    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_usr")
     * })
     */
    private $idUser;

    public function getIdCom(): ?int
    {
        return $this->idCom;
    }

    public function getSujCom(): ?string
    {
        return $this->sujCom;
    }

    public function setSujCom(string $sujCom): self
    {
        $this->sujCom = $sujCom;

        return $this;
    }

    public function getDateCom(): ?\DateTimeInterface
    {
        return $this->dateCom;
    }

    public function setDateCom(\DateTimeInterface $dateCom): self
    {
        $this->dateCom = $dateCom;

        return $this;
    }

    public function getNbReaction(): ?int
    {
        return $this->nbReaction;
    }

    public function setNbReaction(int $nbReaction): self
    {
        $this->nbReaction = $nbReaction;

        return $this;
    }

   

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

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
