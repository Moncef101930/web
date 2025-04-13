<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\CategorieRepository;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\Table(name: 'categorie')]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $description = null;

    // Relation ManyToMany avec Evenement
    #[ORM\ManyToMany(targetEntity: evenement::class, mappedBy: 'categories')]  // Utilisation de "mappedBy" au lieu de "inversedBy"
    private Collection $evenements;  // Utiliser "evenements" sans accent

    public function __construct()
    {
        $this->evenements = new ArrayCollection();  // Initialisation de la collection vide
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    // Getter pour la collection des événements
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    // Ajouter un événement à la catégorie
    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
        }
        return $this;
    }

    // Retirer un événement de la catégorie
    public function removeEvenement(Evenement $evenement): self
    {
        $this->evenements->removeElement($evenement);
        return $this;
    }
}
