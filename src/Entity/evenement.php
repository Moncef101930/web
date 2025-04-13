<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\evenementRepository;
use App\Entity\Categorie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: evenementRepository::class)]
#[ORM\Table(name: 'événements')]
class evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $description = null;

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $lieu = null;

    // Relation ManyToMany avec Categorie
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'evenements')]
    #[ORM\JoinTable(
        name: 'event_cat',
        joinColumns: [
            new ORM\JoinColumn(name: 'idevent', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'idcat', referencedColumnName: 'id')
        ]
    )]
    private Collection $categories;

    public function __construct()
    {
        // Initialisation de la collection de catégories
        $this->categories = new ArrayCollection();
    }

    // Getters et Setters

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    // Getter pour la collection de catégories
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    // Ajouter une catégorie à l'événement
    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categories->contains($categorie)) {
            $this->categories->add($categorie);
        }
        return $this;
    }

    // Retirer une catégorie de l'événement
    public function removeCategorie(Categorie $categorie): self
    {
        $this->categories->removeElement($categorie);
        return $this;
    }

    // Setter pour la collection de catégories (si nécessaire)
    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;
        return $this;
    }
}
