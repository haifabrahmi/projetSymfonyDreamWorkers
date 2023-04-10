<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idUser')]
    private ?int $iduser=null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: "/^[^0-9]*$/",
       message: "Le nom ne doit pas contenir de chiffre"
    )] 
    private ?string $nom= null;
    
    #[ORM\Column]
    #[Assert\Regex(
        pattern: "/^[^0-9]*$/",
       message: "Le prénom ne doit pas contenir de chiffre"
    )] 
    private ?string $prenom= null;
    
    #[ORM\Column]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    private ?string $email= null;
    
    #[ORM\Column]
    #[Assert\Length(exactMessage: "le numéro doit contenir exactement {{ limit }} caractères.",
    exactly:  8)]
    #[Assert\Regex(
        pattern: "/^[^a-zA-Z]+$/",
       message: "Le numéro ne doit pas contenir de lettres"
    )]  
    private ?int $number= null;
   
    #[ORM\Column]
    #[Assert\Length(min: 8, minMessage:"Le mot de passe doit etre supérieur à 8")]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-zA-Z])(?=.*\d).+$/',
        message: "Le mot de passe doit contenir au moins une lettre et un chiffre."
    )]
    private ?string $password= null;
   
    #[ORM\Column]
    private ?string $role= null;

    public function getIduser(): ?int
    {
        return $this->iduser;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }


}