<?php

namespace App\DataFixtures;

use App\Entity\Assessment;
use App\Entity\Building;
use App\Enum\AssessmentDistribution;
use App\Enum\AssessmentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssessmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/assessments.json'), true);

        foreach ($data as $item) {
            $assessment = new Assessment();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $assessment->setBuilding($building);

            $assessment->setDate(new \DateTimeImmutable($item['date']));
            $assessment->setDescription($item['description']);
            $assessment->setTotalAmount($item['total_amount']);
            $assessment->setDistributionMethod(AssessmentDistribution::from($item['distribution_method']));
            $assessment->setStatus(AssessmentStatus::from($item['status']));

            $assessment->setIssuedAt(new \DateTimeImmutable($item['issued_at']));
            $assessment->setDueDate(new \DateTimeImmutable($item['due_date']));

            $this->addReference('assessment_' . $item['id'], $assessment);

            $manager->persist($assessment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BuildingFixtures::class];
    }
}
