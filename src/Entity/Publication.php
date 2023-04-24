<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Publication
 *
 * @ORM\Table(name="publication", indexes={@ORM\Index(name="id_pub", columns={"id_pub"}), @ORM\Index(name="fk_rel_user", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Publication
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_pub", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPub;
    /**
     * @var string
     * @Assert\NotBlank(message="Le titre ne peut pas être vide")
     * @Assert\Length(
     *     max = 50,
     *     min=5,
     *     maxMessage = "Le titre ne peut pas dépasser {{ limit }} caractères",
     *     minMessage = "Le contenu doit dépasser {{ limit }} caractères"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z ]+$/",
     *     message="Le titre ne peut contenir que des lettres et des espaces"
     * )
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     */
    private $titre;
/**
 * @var \Commentaire[]
 *
 * @ORM\OneToMany(mappedBy="idPub", targetEntity=Commentaire::class)
 */
private $commentaires;
    /**
     * @var int
     *
     * @ORM\Column(name="nb_reaction", type="integer", nullable=false)
     */
    private $nbReaction  = '0';

    /**
     * @var string
      *@Assert\NotBlank(message="Le contenu ne peut pas être vide")
     * @Assert\Length(
     *     max = 500,
     *     min=5,
     *     maxMessage = "Le contenu ne peut pas dépasser {{ limit }} caractères"
     * )
     * @ORM\Column(name="texte", type="string", length=255, nullable=false)
     */
    private $texte;
/**
     * @var string|null
     
     * @Assert\Image(
     *mimeTypesMessage="Le fichier doit être une image valide (jpeg, png, gif)")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_pub", type="date", length=50, nullable=false)
     */
    private $datePub ;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->datePub = new \DateTime();
    }
    
    /**
     * @var int
     *
     * @ORM\Column(name="selectionner", type="integer", nullable=false)
     */
    private $selectionner  = '0';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id_usr")
     * })
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="idPub")
     * @ORM\JoinTable(name="pub_like_tracks",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_pub", referencedColumnName="id_pub")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_user", referencedColumnName="id_usr")
     *   }
     * )
     */
    private $idUser = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUser = new \Doctrine\Common\Collections\ArrayCollection();
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
