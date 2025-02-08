<?php
// src/Controller/ApiUserController.php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/api/user')]
#[Route('/api/client')]
class ApiClientController extends AbstractController
{
    #[Route('/list', name: 'api_users', methods: ['GET'])]
    public function getUsers(ClientRepository $userRepository): JsonResponse
    {
        $commandes = $userRepository->findAll();
        $commandesData = array_map(fn(Client $commande) => [
            'id' => $commande->getId(),
            'email' => $commande->getEmail()
        ], $commandes);

        return $this->json($commandesData);
    }
    #[Route('/get', methods: ['GET'])]
    public function index(ClientRepository $userRepository): JsonResponse
    {
        $commandes = $userRepository->findAll();
        $commandesData = array_map(fn(Client $commande) => [
            'id' => $commande->getId(),
            'email' => $commande->getEmail()
        ], $commandes);

        return $this->json($commandesData);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/new', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $user = new Client();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}/edit', methods: ['PUT'])]
    public function update(Request $request, Client $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user->setEmail($data['email'] ?? $user->getEmail());
        if (!empty($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }

        $entityManager->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Client $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'User deleted'], 204);
    }
}
