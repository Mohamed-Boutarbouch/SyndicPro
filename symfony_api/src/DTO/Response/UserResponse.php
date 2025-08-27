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
    public ?string $phoneNumber;

    #[Groups(['user:syndic', 'user:default'])]
    public bool $isActive;

    #[Groups(['user:syndic'])]
    public ?int $buildingId;

    #[Groups(['user:default'])]
    public ?int $syndicId;

    public function __construct(
        int $id,
        string $email,
        ?string $firstName,
        ?string $lastName,
        ?string $phoneNumber,
        bool $isActive,
        ?int $buildingId,
        ?int $syndicId
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->isActive = $isActive;
        $this->buildingId = $buildingId;
        $this->syndicId = $syndicId;
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPhoneNumber(),
            $user->isActive() ?? false,
            $user->getBuilding()?->getId(),
            $user->getSyndic()?->getId()
        );
    }
}
