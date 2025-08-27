<?php

namespace App\Controller;

use App\DTO\Response\UserResponse;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users', name: 'api_user_')]
class UserController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        private UserService $userService
    ) {
        parent::__construct($serializer, $validator);
    }

    #[Route('/syndic/building/{buildingId}', methods: ['GET'], name: 'syndic_by_building')]
    public function getSyndicByBuilding(int $buildingId): JsonResponse
    {
        $syndic = $this->userService->getSyndicByBuildingId($buildingId);

        if (!$syndic) {
            throw new NotFoundHttpException('No syndic found for this building');
        }

        return $this->jsonResponse(
            UserResponse::fromEntity($syndic),
            status: 200,
            groups: ['user:syndic']
        );
    }

    // #[Route('', methods: ['POST'], name: 'create')]
    // public function create(Request $request): JsonResponse
    // {
    //     $dto = $this->serializer->deserialize(
    //         $request->getContent(),
    //         CreateUserRequest::class,
    //         'json'
    //     );
    //
    //     $this->validateRequest($dto);
    //
    //     $user = $this->userService->createUser($dto);
    //     $response = UserResponse::fromEntity($user);
    //
    //     return $this->jsonResponse($response, 201);
    // }
    //
    // #[Route('', methods: ['GET'], name: 'list')]
    // public function list(): JsonResponse
    // {
    //     $users = $this->userService->getUsers();
    //     $response = array_map([UserResponse::class, 'fromEntity'], $users);
    //
    //     return $this->jsonResponse($response);
    // }
    //
    // #[Route('/{id}', methods: ['GET'], name: 'show')]
    // #[UserService("is_granted('VIEW', user)")]
    // public function show(User $user): JsonResponse
    // {
    //     return $this->jsonResponse(UserResponse::fromEntity($user));
    // }
}
