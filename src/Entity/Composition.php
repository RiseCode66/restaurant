<?php
// src/Entity/Composition.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Composition
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Recette::class, inversedBy: 'compositions')]
    #[ORM\JoinColumn(name: 'id_recette', nullable: false)]
    #[MaxDepth(1)]
    #[Groups(["composition:read", "recette:read"])]
    private ?Recette $recette = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'compositions')]
    #[ORM\JoinColumn(name: 'id_ingredient', nullable: false)]
    #[MaxDepth(1)]
    #[Groups(["composition:read", "ingredient:read"])]
    private ?Ingredient $ingredient = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(["composition:read"])]
    private int $quantite;

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;
        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }
}
