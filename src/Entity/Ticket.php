<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ticket')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'utilisateur_nom', type: 'string', length: 255, nullable: false)]
    private string $utilisateurNom;

    #[ORM\Column(name: 'evenement_nom', type: 'string', length: 255, nullable: false)]
    private string $evenementNom;

    #[ORM\Column(name: 'date_achat', type: 'date', nullable: false)]
    private \DateTimeInterface $dateAchat;

    #[ORM\Column(name: 'type_ticket', type: 'string', length: 255, nullable: false)]
    private string $typeTicket;

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUtilisateurNom(): string
    {
        return $this->utilisateurNom;
    }

    public function setUtilisateurNom(string $utilisateurNom): self
    {
        $this->utilisateurNom = $utilisateurNom;
        return $this;
    }

    public function getEvenementNom(): string
    {
        return $this->evenementNom;
    }

    public function setEvenementNom(string $evenementNom): self
    {
        $this->evenementNom = $evenementNom;
        return $this;
    }

    public function getDateAchat(): \DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }

    public function getTypeTicket(): string
    {
        return $this->typeTicket;
    }

    public function setTypeTicket(string $typeTicket): self
    {
        $this->typeTicket = $typeTicket;
        return $this;
    }
}
