<?php
namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Composition;
use App\Repository\PlatRepository;
use App\Repository\CompositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\RecetteRepository;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use SebastianBergmann\Environment\Console;

#[Route('/api/plats')]
class PlatController extends AbstractController
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

    #[Route('/', methods: ['GET'])]
    public function index(PlatRepository $platRepository, SerializerInterface $serializer): JsonResponse
    {
        $plats = $platRepository->findAll();
        return new JsonResponse($serializer->serialize($plats, 'json', ['groups' => 'user:read']), JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Plat $plat, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($plat, 'json', ['groups' => 'user:read']), JsonResponse::HTTP_OK, [], true);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $plat = new Plat();
        $plat->setNom($data['nom']);
        $plat->setPrix($data['prix']);
        $entityManager->persist($plat);
        $entityManager->flush();
        return new JsonResponse($serializer->serialize($plat, 'json', ['groups' => 'user:read']), JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Plat $plat, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $plat->setNom($data['nom']);
        $plat->setPrix($data['prix']);
        $entityManager->flush();
        return new JsonResponse($serializer->serialize($plat, 'json', ['groups' => 'user:read']), JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Plat $plat, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($plat);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/ingredients', methods: ['GET'])]
    public function getIngredients(Plat $plat, CompositionRepository $compositionRepository, SerializerInterface $serializer): JsonResponse
    {
        $compositions = $compositionRepository->findBy(['recette' => $plat->getRecette()]);
        $compo=array_map(fn(Composition $composition) =>[
            'id'=>$composition->getIngredient()->getId(),
            'nom'=>$composition->getIngredient()->getNom(),
            'quantite'=>$composition->getQuantite()
        ],$compositions);
        return new JsonResponse($serializer->serialize($compo, 'json', ['groups' => 'composition:read']), JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/AddIngredient', methods: ['POST'])]
    public function addIngredientPlat(Request $request,CompositionRepository $compositionRepository,IngredientRepository $ingredientRepository,RecetteRepository $recetteRepository): JsonResponse {
    {
        $data = json_decode($request->getContent(), true);
        $composition=new Composition();
        $ingredient=$ingredientRepository->find($data['ingredient']);
        if (!$ingredient) {
            return $this->json(['message' => 'Ingrédient non trouvé'], 404);
        }
        $composition->setIngredient($ingredient);
        $recette=$recetteRepository->find($data['recette']);
        if (!$recette) {
            return $this->json(['message' => 'Recette non trouvé'], 404);
        }
        $composition->setRecette($recette);
        $composition->setQuantite($data['quantite']);
        $this->entityManager->persist($composition);
        $this->entityManager->flush();
        return $this->json(['message' => 'Ingrédient ajouté à la recette', 'composition' => $composition], 201);
    }
}
}