<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Entity\Composition;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Repository\CompositionRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/ingredient')]
final class ApiIngredientController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private IngredientRepository $ingredientRepository;
    private RecetteRepository $recetteRepository;
    private CompositionRepository $compositionRepository;
    private PlatRepository $platRepository;

    public function __construct(EntityManagerInterface $entityManager, 
                                IngredientRepository $ingredientRepository, 
                                RecetteRepository $recetteRepository,
                                CompositionRepository $compositionRepository,
                                PlatRepository $platRepository
                                )
    {
        $this->entityManager = $entityManager;
        $this->ingredientRepository = $ingredientRepository;
        $this->recetteRepository = $recetteRepository;
        $this->compositionRepository = $compositionRepository;
    }

    // 🟢 Ajouter un ingrédient à une recette (Création de la composition)
    #[Route('/get', methods: ['GET'])]
    public function listIngredients(): JsonResponse
    {
        $ingredients = $this->ingredientRepository->findAll();

        $ingredientsData = array_map(fn(Ingredient $ingredient) => [
            'id' => $ingredient->getId(),
            'nom' => $ingredient->getNom(),
            'stock' => $ingredient->getStock(),
        ], $ingredients);

        return $this->json($ingredientsData);
    }

    // 🟢 Créer un nouvel ingrédient
    #[Route('/create', methods: ['POST'])]
    public function createIngredient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom'])) {
            return $this->json(['message' => 'Le nom de l\'ingrédient est requis.'], 400);
        }

        $ingredient = new Ingredient();
        $ingredient->setNom($data['nom']);
        $ingredient->setStock($data['stock']);

        $this->entityManager->persist($ingredient);
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingrédient créé avec succès', 'id' => $ingredient->getId()]);
    }

    // 🟢 Mettre à jour un ingrédient existant
    #[Route('/{id<\d+>}/update', methods: ['PUT'])]
    public function updateIngredient(int $id, Request $request): JsonResponse
    {
        $ingredient = $this->ingredientRepository->find($id);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingrédient non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!empty($data['nom'])) {
            $ingredient->setNom($data['nom']);
        }
        if (!empty($data['stock'])) {
            $ingredient->setStock($data['stock']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingrédient mis à jour avec succès']);
    }

    // 🔴 Supprimer un ingrédient
    #[Route('/{id<\d+>}/delete', methods: ['DELETE'])]
    public function deleteIngredient(int $id): JsonResponse
    {
        $ingredient = $this->ingredientRepository->find($id);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingrédient non trouvé'], 404);
        }

        $this->entityManager->remove($ingredient);
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingrédient supprimé avec succès']);
    }
    // 🟢 Lister les ingrédients par plat (Recette)
    #[Route('/plat/{platId<\d+>}', name: 'list_by_plat', methods: ['GET'])]
    public function listIngredientsByPlat(int $platId): JsonResponse
    {
        // Trouver la recette associée au plat
        $plat = $this->platRepository->find($platId);
        if (!$plat) {
            return $this->json(['message' => 'Plat introuvable'], 404);
        }

        $recette = $plat->getRecette();
        $compositions = $this->compositionRepository->findBy(['recette' => $recette]);

        $ingredients = array_map(fn(Composition $composition) => [
            'ingredient_id' => $composition->getIngredient()->getId(),
            'ingredient_nom' => $composition->getIngredient()->getNom(),
            'quantite' => $composition->getQuantite(),
        ], $compositions);

        return $this->json($ingredients);
    }
}
