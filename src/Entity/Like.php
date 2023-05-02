<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:"likepub")]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "idUser")]
    private $user;

    
    #[ORM\ManyToOne(targetEntity: Publication::class)]
    #[ORM\JoinColumn(name: "pub_id", referencedColumnName: "id_pub")]
    private $publication;

    #[ORM\Column(length: 255)]
    private ?string $type;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}


