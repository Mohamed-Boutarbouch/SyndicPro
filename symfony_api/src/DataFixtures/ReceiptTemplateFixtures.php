<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\ReceiptTemplate;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptTemplateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/receipt_templates.json'), true);

        foreach ($data as $item) {
            $receiptTemplate = new ReceiptTemplate();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $receiptTemplate->setBuilding($building);

            $receiptTemplate->setName($item['name']);
            $receiptTemplate->setPlaceholders($item['placeholders']);


            $receiptTemplate->setfilePath($item['file_path']);

            $user = $this->getReference('user_' . $item['created_by'], User::class);
            $receiptTemplate->setCreatedBy($user);

            $manager->persist($receiptTemplate);

            $this->addReference('receipt_template_' . $item['id'], $receiptTemplate);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BuildingFixtures::class,
            UserFixtures::class
        ];
    }
}
