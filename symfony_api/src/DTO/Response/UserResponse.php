<?php

namespace App\DTO\Response;

use App\Entity\User;

class UserResponse
{
    public function __construct(
        public int $id,
        public string $email,
        // public string $firstName,
        // public string $lastName,
        // public \DateTimeInterface $createdAt
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            // $user->getFirstName(),
            // $user->getLastName(),
            // $user->getCreatedAt()
        );
    }
}
