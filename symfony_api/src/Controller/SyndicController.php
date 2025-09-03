<?php

namespace App\Controller;

use App\DTO\Response\UserResponse;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_user_')]
class SyndicController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    #[Route('/syndic/{userId}/building', methods: ['GET'], name: 'syndic_by_building')]
    public function getSyndicByBuilding(int $userId): JsonResponse
    {
        $syndic = $this->userRepository->findBuildingBySyndic($userId);

        if (!$syndic) {
            throw new NotFoundHttpException('No syndic found for this building');
        }

        return $this->json(
            UserResponse::fromEntity($syndic),
            status: 200,
            context: ['groups' => ['user:syndic']]
        );
    }
}
