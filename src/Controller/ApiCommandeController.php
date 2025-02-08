<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/commande')]
final class ApiCommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CommandeRepository $commandeRepository;
    private PlatRepository $platRepository;

    public function __construct(EntityManagerInterface $entityManager, 
                                CommandeRepository $commandeRepository,
                                PlatRepository $platRepository)
    {
        $this->entityManager = $entityManager;
        $this->commandeRepository = $commandeRepository;
        $this->platRepository = $platRepository;
    }
    #[Route('/', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): JsonResponse
    {
        return $this->json($commandeRepository->findAll(), 200, [], ['groups' => 'user:read']);
    }
    #[Route('/get', methods: ['GET'])]
    public function listCommande(): JsonResponse
    {
        $plats = $this->commandeRepository->findAll();
        $platsData = array_map(fn(Commande $plat) => [
            'id' => $plat->getId(),
            'plat' => $plat->getPlat(),
            'client' => $plat->getClient(),
            'date' => $plat->getDate(),
            'etat' => $plat->getEtat()
        ], $plats);

        return $this->json($platsData);
    }

    // 🟢 Créer une nouvelle commande
    #[Route('/creer', name: 'create', methods: ['POST'])]
    public function createCommande(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['id_plat']) || empty($data['date'])) {
            return $this->json(['message' => 'Données manquantes'], 400);
        }

        $plat = $this->platRepository->find($data['id_plat']);
        if (!$plat) {
            return $this->json(['message' => 'Plat introuvable'], 404);
        }

        $commande = new Commande();
        $commande->setPlat($plat);
        $commande->setDate(new \DateTime($data['date']));
        $commande->setEtat(0); // Par défaut, l'état est 0 (en attente)

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $this->json(['message' => 'Commande créée', 'commande' => $commande], 201);
    }

    // 🟢 Lister toutes les commandes
    #[Route('/lister', name: 'list', methods: ['GET'])]
    public function listCommandes(): JsonResponse
    {
        $commandes = $this->commandeRepository->findAll();
        $commandesData = array_map(fn(Commande $commande) => [
            'id' => $commande->getId(),
            'plat' => $commande->getPlat()->getNom(),
            'date' => $commande->getDate()->format('Y-m-d H:i:s'),
            'etat' => $commande->getEtat()
        ], $commandes);

        return $this->json($commandesData);
    }

    // 🟢 Changer l'état d'une commande
    #[Route('/etat/{id<\d+>}', name: 'update_etat', methods: ['PUT'])]
    public function updateEtat(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['etat'])) {
            return $this->json(['message' => 'État manquant'], 400);
        }

        $commande = $this->commandeRepository->find($id);
        if (!$commande) {
            return $this->json(['message' => 'Commande introuvable'], 404);
        }

        // On met à jour l'état de la commande (0: en attente, 1: validée, 2: livrée, etc.)
        $commande->setEtat($data['etat']);

        $this->entityManager->flush();

        return $this->json(['message' => 'État de la commande mis à jour', 'commande' => $commande]);
    }

    // 🔴 Supprimer une commande
    #[Route('/supprimer/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function deleteCommande(int $id): JsonResponse
    {
        $commande = $this->commandeRepository->find($id);
        if (!$commande) {
            return $this->json(['message' => 'Commande introuvable'], 404);
        }

        $this->entityManager->remove($commande);
        $this->entityManager->flush();

        return $this->json(['message' => 'Commande supprimée']);
    }
}
