<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use App\Entity\User;
use App\Entity\Publication;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Commentaire
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCom;

    
    #[ORM\Column(length: 255)]
    #[Assert\Length(max : 500,
     maxMessage :"Le contenu ne peut pas dépasser {{ limit }} caractères",  
     min:5,
     minMessage :"Le contenu doit dépasser {{ limit }} caractères",   
     )]
    private ?string $sujCom;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $dateCom ;

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->dateCom = new \DateTime();
    }
    #[ORM\Column]
    private ?int $nbReaction = 0;


    #[ORM\ManyToOne(targetEntity: Publication::class, inversedBy:"commentaires")]
    #[ORM\JoinColumn(name: "id_pub", referencedColumnName: "id_pub")]
    private $idPub;

     
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "idUser")]
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
