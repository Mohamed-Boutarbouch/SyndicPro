<?php

namespace App\DataFixtures;

use App\Entity\Building;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BuildingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $json = file_get_contents(__DIR__ . '/data/buildings.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $building = new Building();
            $building->setName($item['name']);
            $building->setAddress($item['address']);
            $building->setDescription($item['description'] ?? null);

            $manager->persist($building);

            $this->addReference('building_' . $item['id'], $building);
        }

        $manager->flush();
    }
}
