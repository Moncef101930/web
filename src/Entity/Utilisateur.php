<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Nom cannot be blank.")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Nom must be at least {{ limit }} characters long.",
        maxMessage: "Nom cannot be longer than {{ limit }} characters."
    )]
    private string $nom;

    #[ORM\Column(name: 'prenom', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Prenom cannot be blank.")]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: "Prenom must be at least {{ limit }} characters long.",
        maxMessage: "Prenom cannot be longer than {{ limit }} characters."
    )]
    private string $prenom;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Email cannot be blank.")]
    #[Assert\Email(message: "Please enter a valid email address.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Email must be at least {{ limit }} characters long.",
        maxMessage: "Email cannot be longer than {{ limit }} characters."
    )]
    private string $email;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Password cannot be blank.")]
    #[Assert\Length(
        min: 6,
        max: 255,
        minMessage: "Password must be at least {{ limit }} characters long.",
        maxMessage: "Password cannot be longer than {{ limit }} characters."
    )]
    private string $motDePasse;

    #[ORM\Column(name: 'role', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Role cannot be blank.")]
    private string $role;

    #[ORM\Column(name: 'date_naissance', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "Date de naissance is required.")]

    private ?\DateTimeInterface $dateNaissance;

    #[ORM\Column(name: 'bio', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Bio cannot be blank.")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Bio must be at least {{ limit }} characters long.",
        maxMessage: "Bio cannot be longer than {{ limit }} characters."
    )]
    private string $bio;

    #[ORM\Column(name: 'image', type: 'string', length: 255, nullable: false)]
    
    private string $image;

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
