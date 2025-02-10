<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/plat')]
final class ApiPlatController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PlatRepository $platRepository;
    private RecetteRepository $recetteRepository;

    public function __construct(EntityManagerInterface $entityManager, 
                                PlatRepository $platRepository, 
                                RecetteRepository $recetteRepository)
    {
        $this->entityManager = $entityManager;
        $this->platRepository = $platRepository;
        $this->recetteRepository = $recetteRepository;
    }

    // 🟢 Créer un plat
    #[Route('/ajouter', name: 'add', methods: ['POST'])]
    public function addPlat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prix']) || empty($data['id_recette'])) {
            return $this->json(['message' => 'Données manquantes'], 400);
        }

        $recette = $this->recetteRepository->find($data['id_recette']);
        if (!$recette) {
            return $this->json(['message' => 'Recette introuvable'], 404);
        }

        $plat = new Plat();
        $plat->setNom($data['nom']);
        $plat->setPrix($data['prix']);
        $plat->setRecette($recette);

        $this->entityManager->persist($plat);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plat ajouté', 'plat' => $plat], 201);
    }

    // 🟢 Lister tous les plats
    #[Route('/', methods: ['GET'])]
    public function listPlats(): JsonResponse
    {
        $plats = $this->platRepository->findAll();

        $platsData = array_map(fn(Plat $plat) => [
            'id' => $plat->getId(),
            'nom' => $plat->getNom(),
            'prix' => $plat->getPrix(),
            'recette' => $plat->getRecette(),
        ], $plats);

        return $this->json($platsData);
    }
    #[Route('/get', methods: ['GET'])]
    public function listPlat(): JsonResponse
    {
        $plats = $this->platRepository->findAll();

        $platsData = array_map(fn(Plat $plat) => [
            'id' => $plat->getId(),
            'nom' => $plat->getNom(),
            'prix' => $plat->getPrix(),
            'recette' => $plat->getRecette(),
        ], $plats);

        return $this->json($platsData);
    }

    // 🟢 Détails d'un plat spécifique
    #[Route('/{id<\d+>}', name: 'detail', methods: ['GET'])]
    public function getPlat(int $id): JsonResponse
    {
        $plat = $this->platRepository->find($id);

        if (!$plat) {
            return $this->json(['message' => 'Plat introuvable'], 404);
        }

        return $this->json([
            'id' => $plat->getId(),
            'nom' => $plat->getNom(),
            'prix' => $plat->getPrix(),
            'recette_id' => $plat->getRecette()->getId(),
        ]);
    }

    // 🔴 Mettre à jour un plat
    #[Route('/{id<\d+>}/modifier', name: 'edit', methods: ['PUT'])]
    public function editPlat(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $plat = $this->platRepository->find($id);
        if (!$plat) {
            return $this->json(['message' => 'Plat introuvable'], 404);
        }

        // Modifier les informations du plat
        if (!empty($data['nom'])) {
            $plat->setNom($data['nom']);
        }

        if (!empty($data['prix'])) {
            $plat->setPrix($data['prix']);
        }

        if (!empty($data['id_recette'])) {
            $recette = $this->recetteRepository->find($data['id_recette']);
            if ($recette) {
                $plat->setRecette($recette);
            } else {
                return $this->json(['message' => 'Recette introuvable'], 404);
            }
        }

        $this->entityManager->flush();

        return $this->json(['message' => 'Plat mis à jour', 'plat' => $plat]);
    }

    // 🔴 Supprimer un plat
    #[Route('/{id<\d+>}/supprimer', name: 'delete', methods: ['DELETE'])]
    public function deletePlat(int $id): JsonResponse
    {
        $plat = $this->platRepository->find($id);
        if (!$plat) {
            return $this->json(['message' => 'Plat introuvable'], 404);
        }

        $this->entityManager->remove($plat);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plat supprimé']);
    }
}
