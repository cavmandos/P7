<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class MobileController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des téléphones mobiles.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des mobiles",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Mobile::class))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=401,
     *     description="Non autorisé : JWT expiré",
     *     @OA\JsonContent(
     *        @OA\Property(
     *         property="code",
     *         type="integer",
     *         example="401"
     *        ),
     *        @OA\Property(
     *         property="message",
     *         type="string",
     *         example="JWT expiré"
     *        ),
     *     )
     * )
     * 
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
     * @OA\Tag(name="Mobiles")
     *
     * @param MobileRepository $mobileRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/mobiles', name: 'mobile', methods: ['GET'])]
    public function getMobileList(MobileRepository $mobileRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getMobileList-" . $page . "-" . $limit;
        $mobileList = $cachePool->get($idCache, function (ItemInterface $item) use ($mobileRepository, $page, $limit) {
            $item->tag("booksCache");
            return $mobileRepository->findAllWithPagination($page, $limit);
        });

        $jsonMobileList = $serializer->serialize($mobileList, 'json');
        return new JsonResponse($jsonMobileList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récupérer le détail d'un téléphone mobile.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un mobile",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Mobile::class))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Pas de produit trouvé à cet identifiant",
     *     @OA\JsonContent(
     *        @OA\Property(
     *         property="error",
     *         type="string",
     *         example="Ce produit n'éxiste pas"
     *        )
     *     )
     * )
     * 
     * @OA\Response(
     *     response=401,
     *     description="Non autorisé : JWT expiré",
     *     @OA\JsonContent(
     *        @OA\Property(
     *         property="code",
     *         type="integer",
     *         example="401"
     *        ),
     *        @OA\Property(
     *         property="message",
     *         type="string",
     *         example="JWT expiré"
     *        ),
     *     )
     * )
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Le mobile que l'on souhaite récupérer"
     * )
     *
     * @OA\Tag(name="Mobiles")
     *
     * @param Mobile $mobile
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/mobiles/{id}', name: 'detailMobile', methods: ['GET'])]
    public function getDetailMobile(Mobile $mobile, SerializerInterface $serializer): JsonResponse 
    {
        $jsonMobile = $serializer->serialize($mobile, 'json');
        return new JsonResponse($jsonMobile, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
