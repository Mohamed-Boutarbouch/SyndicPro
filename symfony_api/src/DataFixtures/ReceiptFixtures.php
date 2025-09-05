<?php

namespace App\DataFixtures;

use App\Entity\Receipt;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/receipts.json'), true);

        foreach ($data as $item) {
            $receipt = new Receipt();

            $receipt->setFilePath($item['filePath']);

            if (!empty($item['placeholders'])) {
                $receipt->setPlaceholders($item['placeholders']);
            }

            $user = $this->getReference('user_' . $item['created_by_id'], User::class);
            $receipt->setCreatedBy($user);

            $manager->persist($receipt);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
