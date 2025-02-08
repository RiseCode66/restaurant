<?php

namespace App\Controller;

use App\Entity\Composition;
use App\Entity\Recette;
use App\Entity\Ingredient;
use App\Repository\CompositionRepository;
use App\Repository\RecetteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/composition')]
final class ApiCompositionController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CompositionRepository $compositionRepository;
    private RecetteRepository $recetteRepository;
    private IngredientRepository $ingredientRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                CompositionRepository $compositionRepository,
                                RecetteRepository $recetteRepository,
                                IngredientRepository $ingredientRepository)
    {
        $this->entityManager = $entityManager;
        $this->compositionRepository = $compositionRepository;
        $this->recetteRepository = $recetteRepository;
        $this->ingredientRepository = $ingredientRepository;
    }
    #[Route('/get', methods: ['GET'])]
    public function listPlats(): JsonResponse
    {
        $plats = $this->compositionRepository->findAll();

        $platsData = array_map(fn(Composition $plat) => [
            'recette' => $plat->getRecette(),
            'ingredient' => $plat->getIngredient(),
        ], $plats);

        return $this->json($platsData);
    }
    #[Route('/', methods: ['GET'])]
    public function index(CompositionRepository $compositionRepository): JsonResponse
    {
        return $this->json($compositionRepository->findAll(), 200, [], ['groups' => 'user:read']);
    }

    // 🟢 Ajouter un ingrédient à une recette
    #[Route('/ajouter', name: 'add', methods: ['POST'])]
    public function addComposition(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['id_recette']) || empty($data['id_ingredient']) || empty($data['quantite'])) {
            return $this->json(['message' => 'Données manquantes'], 400);
        }

        $recette = $this->recetteRepository->find($data['id_recette']);
        if (!$recette) {
            return $this->json(['message' => 'Recette introuvable'], 404);
        }

        $ingredient = $this->ingredientRepository->find($data['id_ingredient']);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingrédient introuvable'], 404);
        }

        $composition = new Composition();
        $composition->setRecette($recette);
        $composition->setIngredient($ingredient);
        $composition->setQuantite($data['quantite']);

        $this->entityManager->persist($composition);
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingrédient ajouté à la recette', 'composition' => $composition], 201);
    }

    // 🟢 Lister les ingrédients d'une recette
    #[Route('/recette/{id_recette<\d+>}', name: 'list_by_recette', methods: ['GET'])]
    public function listIngredientsByRecette(int $id_recette): JsonResponse
    {
        $recette = $this->recetteRepository->find($id_recette);
        if (!$recette) {
            return $this->json(['message' => 'Recette introuvable'], 404);
        }

        $compositions = $this->compositionRepository->findBy(['recette' => $recette]);
        $ingredientsData = array_map(fn(Composition $composition) => [
            'ingredient_id' => $composition->getIngredient()->getId(),
            'ingredient_nom' => $composition->getIngredient()->getNom(),
            'quantite' => $composition->getQuantite(),
        ], $compositions);

        return $this->json($ingredientsData);
    }

    // 🔴 Supprimer un ingrédient d'une recette
    #[Route('/supprimer/{id}/{id2}', methods: ['DELETE'], requirements: ['id' => '\d+', 'id2' => '\d+'])]
    public function deleteComposition(int $id,int $id2): JsonResponse
    {
        $composition = $this->compositionRepository->findBY(['recette' => $id,'ingredient'=>$id2]);
        if (!$composition) {
            return $this->json(['message' => 'Composition introuvable'], 404);
        }

        $this->entityManager->remove($composition[0]);
        $this->entityManager->flush();

        return $this->json(['message' => 'Ingrédient supprimé de la recette']);
    }
}
