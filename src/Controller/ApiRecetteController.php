<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/recette', name: 'api_recette_')]
final class ApiRecetteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private RecetteRepository $recetteRepository;

    public function __construct(EntityManagerInterface $entityManager, RecetteRepository $recetteRepository)
    {
        $this->entityManager = $entityManager;
        $this->recetteRepository = $recetteRepository;
    }

    // 🟢 GET toutes les recettes
    #[Route('/', name: 'list', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $recettes = $this->recetteRepository->findAll();

        $data = array_map(fn(Recette $recette) => [
            'id' => $recette->getId(),
            'nom' => $recette->getNom(),
        ], $recettes);

        return $this->json($data);
    }

    // 🟢 GET une seule recette
    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        $recette = $this->recetteRepository->find($id);

        if (!$recette) {
            return $this->json(['message' => 'Recette non trouvée'], 404);
        }

        return $this->json([
            'id' => $recette->getId(),
            'nom' => $recette->getNom(),
        ]);
    }

    // 🟢 POST : Ajouter une recette
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['nom'])) {
            return $this->json(['message' => 'Nom manquant'], 400);
        }

        $recette = new Recette();
        $recette->setNom($data['nom']);

        $this->entityManager->persist($recette);
        $this->entityManager->flush();

        return $this->json(['message' => 'Recette ajoutée', 'id' => $recette->getId()], 201);
    }

    // 🟡 PUT : Modifier une recette
    #[Route('/{id<\d+>}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $recette = $this->recetteRepository->find($id);

        if (!$recette) {
            return $this->json(['message' => 'Recette non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!empty($data['nom'])) {
            $recette->setNom($data['nom']);
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Recette mise à jour']);
    }

    // 🔴 DELETE : Supprimer une recette
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $recette = $this->recetteRepository->find($id);

        if (!$recette) {
            return $this->json(['message' => 'Recette non trouvée'], 404);
        }

        $this->entityManager->remove($recette);
        $this->entityManager->flush();

        return $this->json(['message' => 'Recette supprimée']);
    }
}
