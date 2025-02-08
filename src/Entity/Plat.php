<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlatRepository;
use Symfony\Component\Serializer\Annotation\Groups;
#[Groups(["user:read"])]

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'float')]
    private float $prix;

    // Relation One-to-One avec Recette
    #[ORM\OneToOne(targetEntity: Recette::class)]
    #[ORM\JoinColumn(name: 'id_recette', referencedColumnName: 'id', nullable: false)]
    private ?Recette $recette = null;

    // Getters et setters
    public function getId(): ?int
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

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;
        return $this;
    }
}
