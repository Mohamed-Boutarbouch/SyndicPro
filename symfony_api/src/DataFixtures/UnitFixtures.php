<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\UnitType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UnitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/units.json'), true);

        foreach ($data as $item) {
            $unit = new Unit();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $unit->setBuilding($building);

            $unit->setType(UnitType::from($item['type']));
            $unit->setFloor($item['floor']);
            $unit->setNumber($item['number']);

            $user = $this->getReference('user_' . $item['user_id'], User::class);
            $unit->setUser($user);

            $unit->setStartDate(new \DateTimeImmutable($item['start_date']));
            $unit->setEndDate(new \DateTimeImmutable($item['end_date']));

            $this->addReference('unit_' . $item['id'], $unit);

            $manager->persist($unit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BuildingFixtures::class, UserFixtures::class];
    }
}
