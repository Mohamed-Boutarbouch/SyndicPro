<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\User;

class UserResponse
{
    #[Groups(['user:syndic', 'user:default'])]
    public int $id;

    #[Groups(['user:syndic', 'user:default'])]
    public string $email;

    #[Groups(['user:syndic', 'user:default'])]
    public ?string $firstName;

    #[Groups(['user:syndic', 'user:default'])]
    public ?string $lastName;

    #[Groups(['user:syndic', 'user:default'])]
    public ?string $fullName;

    #[Groups(['user:syndic', 'user:default'])]
    public array $roles = [];

    #[Groups(['user:syndic', 'user:default'])]
    public ?string $phoneNumber;

    #[Groups(['user:syndic', 'user:default'])]
    public bool $isActive;

    #[Groups(['user:syndic'])]
    public ?BuildingResponse $building = null;

    #[Groups(['user:syndic'])]
    public ?int $buildingId;

    #[Groups(['user:default'])]
    public ?int $syndicId;

    public static function fromEntity(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->fullName = trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? ''));
        $dto->phoneNumber = $user->getPhoneNumber();
        $dto->roles = $user->getRoles();
        $dto->syndicId = $user->getSyndic()?->getId();

        if ($user->getBuilding()) {
            $dto->building = BuildingResponse::fromEntity($user->getBuilding());
        }

        return $dto;
    }
}
