<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\RegularContribution;
use App\Entity\User;
use App\Enum\ContributionStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RegularContributionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/regular_contributions.json'), true);

        foreach ($data as $item) {
            $regularContribution = new RegularContribution();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $regularContribution->setBuilding($building);

            $regularContribution->setYear($item['year']);
            $regularContribution->setTotalAnnualAmount($item['total_annual_amount']);
            $regularContribution->setAmountPerUnit($item['amount_per_unit']);
            $regularContribution->setStartDate(new \DateTimeImmutable($item['start_date']));
            $regularContribution->setEndDate(new \DateTimeImmutable($item['end_date']));
            $regularContribution->setStatus(ContributionStatus::from($item['status']));

            $user = $this->getReference('user_' . $item['created_by_id'], User::class);
            $regularContribution->setCreatedBy($user);

            $this->addReference('regular_contribution_' . $item['id'], $regularContribution);

            $manager->persist($regularContribution);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BuildingFixtures::class, UserFixtures::class];
    }
}
