<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/users.json'), true);

        foreach ($data as $item) {
            $user = new User();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $user->setBuilding($building);

            $user->setEmail($item['email']);
            $user->setPassword($item['password_hash']);
            $user->setPhoneNumber($item['phone_number']);
            $user->setFirstName($item['first_name']);
            $user->setLastName($item['last_name']);

            if (!empty($item['roles'])) {
                $user->setRoles($item['roles']);
            }

            if (!empty($item['syndic_id'])) {
                $syndic = $this->getReference('user_' . $item['syndic_id'], User::class);
                $user->setSyndic($syndic);
            }

            $user->setIsActive((bool) $item['is_active']);

            $this->addReference('user_' . $item['id'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BuildingFixtures::class];
    }
}
