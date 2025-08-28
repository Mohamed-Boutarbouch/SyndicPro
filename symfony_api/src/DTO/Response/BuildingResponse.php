<?php

namespace App\DTO\Response;

use App\Entity\Building;
use Symfony\Component\Serializer\Annotation\Groups;

class BuildingResponse
{
    #[Groups(['user:syndic'])]
    public int $id;

    #[Groups(['user:syndic'])]
    public string $name;

    #[Groups(['user:syndic'])]
    public ?string $address = null;

    #[Groups(['user:syndic'])]
    public ?string $description = null;

    public static function fromEntity(Building $building): self
    {
        $dto = new self();
        $dto->id = $building->getId();
        $dto->name = $building->getName();
        $dto->address = $building->getAddress();
        $dto->description = $building->getDescription();

        return $dto;
    }
}
