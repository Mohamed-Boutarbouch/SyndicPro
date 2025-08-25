<?php

namespace App\Controller\Api;

use App\DTO\Request\CreateUserRequest;
use App\DTO\Response\UserResponse;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            CreateUserRequest::class,
            'json'
        );

        $this->validateRequest($dto);

        $user = $this->userService->createUser($dto);
        $response = UserResponse::fromEntity($user);

        return $this->jsonResponse($response, 201);
    }

    #[Route('', methods: ['GET'], name: 'list')]
    public function list(): JsonResponse
    {
        $users = $this->userService->getUsers();
        $response = array_map([UserResponse::class, 'fromEntity'], $users);

        return $this->jsonResponse($response);
    }

    #[Route('/{id}', methods: ['GET'], name: 'show')]
    #[UserService("is_granted('VIEW', user)")]
    public function show(User $user): JsonResponse
    {
        return $this->jsonResponse(UserResponse::fromEntity($user));
    }
}
