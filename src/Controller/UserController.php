<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des utilisateurs.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class,groups={"getUser"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'user', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool, TokenStorageInterface $token): JsonResponse
    {
        $customer = $token->getToken()->getUser();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = "getUserList-" . $page . "-" . $limit;
        $userList = $cachePool->get($idCache, function (ItemInterface $item) use ($userRepository, $page, $limit, $customer) {
            $item->tag("usersCache");
            return $userRepository->findAllWithPagination($customer, $page, $limit);
        });
        $context = SerializationContext::create()->setGroups(['getUser']);
        $jsonUserList = $serializer->serialize($userList, 'json', $context);
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récupérer le détail d'utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class,groups={"getUser"}))
     *     )
     * )
     *
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser(User $user, SerializerInterface $serializer, TagAwareCacheInterface $cachePool, TokenStorageInterface $token): JsonResponse 
    {
        /** @var Customer $customer */
        $customer = $token->getToken()->getUser();

        if ($user->getCustomer()->getEmail() === $customer->getUserIdentifier()) {
            $idCache = "getUserDetail-" . $user->getId();
            $userDetail = $cachePool->get($idCache, function (ItemInterface $item) use ($user, $serializer) {
                $item->tag("userCache");
                $context = SerializationContext::create()->setGroups(['getUser']);
                return $serializer->serialize($user, 'json', $context);
            });

            return new JsonResponse($userDetail, Response::HTTP_OK, ['accept' => 'json'], true);
        } else {
            return new JsonResponse(json_encode(['message' => "Erreur. Cet utilisateur n'est pas répertorié chez vous. "]), Response::HTTP_UNAUTHORIZED, [], true);
        }
        
    }

    /**
     * Cette méthode permet de supprimer un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Supprimer un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class,groups={"getUser"}))
     *     )
     * )
     *
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un utilisateur')]
    public function deleteUser(User $user, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cachePool, TokenStorageInterface $token): JsonResponse 
    {
        $customer = $token->getToken()->getUser();

        if ($user->getCustomer()->getEmail() === $customer->getUserIdentifier()) {
            $cachePool->invalidateTags(["usersCache"]);
            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } else {
            return new JsonResponse(json_encode(['message' => 'Erreur. Vous ne pouvez pas supprimer un client qui ne vous appartient pas !']), Response::HTTP_UNAUTHORIZED, [], true);
        }
        
    }

    /**
     * Cette méthode permet de créer un utilisateur.
     *
     * @OA\Response(
     *     response=200,
     *     description="Créer un utilisateur",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class,groups={"getUser"}))
     *     )
     * )
     *
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users', name:"createUser", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un utilisateur')]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TokenStorageInterface $token): JsonResponse 
    {
        /** @var User $user  */ 
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setCreatedAt(new \DateTimeImmutable());
        $customer = $token->getToken()->getUser();
        $user->setCustomer($customer);
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();
        $context = SerializationContext::create()->setGroups(['getUser']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
   }

}
