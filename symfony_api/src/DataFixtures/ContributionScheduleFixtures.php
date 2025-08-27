<?php

namespace App\DataFixtures;

use App\Entity\ContributionSchedule;
use App\Entity\RegularContribution;
use App\Entity\Unit;
use App\Enum\ContributionFrequency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContributionScheduleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/contribution_schedules.json'), true);

        foreach ($data as $item) {
            $contributionSchedule = new ContributionSchedule();

            $regularContribution = $this->getReference(
                'regular_contribution_' . $item['regular_contribution_id'],
                RegularContribution::class
            );
            $contributionSchedule->setRegularContribution($regularContribution);

            $unit = $this->getReference('unit_' . $item['unit_id'], Unit::class);
            $contributionSchedule->setUnit($unit);

            $contributionSchedule->setFrequency(ContributionFrequency::from($item['frequency']));
            $contributionSchedule->setAmountPerPayment($item['amount_per_payment']);
            $contributionSchedule->setNextDueDate(new \DateTimeImmutable($item['next_due_date']));
            $contributionSchedule->setIsActive($item['is_active']);

            $this->addReference('contribution_schedule_' . $item['id'], $contributionSchedule);

            $manager->persist($contributionSchedule);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RegularContributionFixtures::class, UnitFixtures::class];
    }
}
