<?php

namespace App\Service;

use App\DTO\Request\CreateUserRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createUser(CreateUserRequest $request): User
    {
        $user = new User();
        $user->setEmail($request->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));
        // $user->setFirstName($request->firstName);
        // $user->setLastName($request->lastName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getSyndicByBuildingId(int $buildingId): ?User
    {
        return $this->userRepository->findSyndicByBuilding($buildingId);
    }
}
