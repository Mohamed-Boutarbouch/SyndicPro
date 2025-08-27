<?php

namespace App\DataFixtures;

use App\Entity\Assessment;
use App\Entity\AssessmentItem;
use App\Entity\Unit;
use App\Enum\AssessmentItemStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssessmentItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/assessment_items.json'), true);

        foreach ($data as $item) {
            $assessmentItem = new AssessmentItem();

            $assessment = $this->getReference('assessment_' . $item['assessment_id'], Assessment::class);
            $assessmentItem->setAssessment($assessment);

            $unit = $this->getReference('unit_' . $item['unit_id'], Unit::class);
            $assessmentItem->setUnit($unit);

            $assessmentItem->setAmount($item['amount']);
            $assessmentItem->setStatus(AssessmentItemStatus::from($item['status']));

            $this->addReference('assessment_item_' . $item['id'], $assessmentItem);

            $manager->persist($assessmentItem);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AssessmentFixtures::class,
            UnitFixtures::class
        ];
    }
}
