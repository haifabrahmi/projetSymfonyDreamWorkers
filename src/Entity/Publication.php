<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PublicationRepository;
use DateTimeInterface;
use App\Entity\Commentaire;
use App\Entity\User;


#[ORM\Entity(repositoryClass: PublicationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPub;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max : 10,
     maxMessage :"Le contenu ne peut pas dépasser {{ limit }} caractères",  
     min:4,
     minMessage :"Le contenu doit dépasser {{ limit }} caractères",   
     )]
    private ?string $titre;

#[ORM\OneToMany(mappedBy: "idPub", targetEntity: Commentaire::class)]
private $commentaires;

#[ORM\Column]
    private ?int $nbReaction  = 0;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max : 500,
     maxMessage :"Le contenu ne peut pas dépasser {{ limit }} caractères",  
     min:5,
     minMessage :"Le contenu doit dépasser {{ limit }} caractères",   
     )]
    private ?string  $texte;
    #[ORM\Column(length: 255)]
    private ?string  $image;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $datePub ;

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->datePub = new \DateTime();
    }
    
    #[ORM\Column]
    private ?int $selectionner  = 0;

  
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "idUser")]
    private $user;

   
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "idUser")]
    private $idUser ;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getIdPub(): ?int
    {
        return $this->idPub;
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getDatePub(): ?\DateTimeInterface
    {
        return $this->datePub;
    }

    public function setDatePub(\DateTimeInterface $datePub): self
    {
        $this->datePub = $datePub;

        return $this;
    }

    public function getSelectionner(): ?int
    {
        return $this->selectionner;
    }

    public function setSelectionner(int $selectionner): self
    {
        $this->selectionner = $selectionner;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->idUser;
    }

    public function addIdUser(User $idUser): self
    {
        if (!$this->idUser->contains($idUser)) {
            $this->idUser->add($idUser);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        $this->idUser->removeElement($idUser);

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setIdPub($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getIdPub() === $this) {
                $commentaire->setIdPub(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
     

}
