<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
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

    #[ORM\Column]
    private ?string $token="";

    #[ORM\Column]
    private ?int $is_verified=0;
     
    

    public function getIs_verified(): ?int
    {
        return $this->is_verified;
    }
    public function setIs_verified(int $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }
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
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function setToken(string $token): self
    {
        $this->token = $token;

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

   
   
    public function getUserIdentifier(): string
    {
        return $this->email; // Remplacez avec le champ qui représente l'identifiant unique de l'utilisateur
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
        // Retournez un tableau de rôles auxquels l'utilisateur appartient
        // Par exemple : return ['ROLE_USER'];
    }


    public function getSalt(): ?string
    {
        // Vous pouvez générer et retourner une valeur de sel aléatoire ici
        // ou retourner null si vous n'utilisez pas de sel
        return null;
    }

    public function eraseCredentials()
    {
        // Vous pouvez implémenter cette méthode pour effacer toute donnée sensible de l'utilisateur,
        // par exemple, le mot de passe en clair après l'authentification
    }

    public function getUsername(): string
    {
        return $this->nom; // Remplacez avec le champ qui représente le nom d'utilisateur de l'utilisateur
    }

	
	/**
	 * @param int|null $iduser 
	 * @return self
	 */
	public function setIduser(?int $iduser): self {
		$this->iduser = $iduser;
		return $this;
	}
}