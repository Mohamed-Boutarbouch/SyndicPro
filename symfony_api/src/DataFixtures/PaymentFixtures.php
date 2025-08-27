<?php

namespace App\DataFixtures;

use App\Entity\AssessmentItem;
use App\Entity\ContributionSchedule;
use App\Entity\Payment;
use App\Entity\User;
use App\Enum\PaymentMethod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/payments.json'), true);

        foreach ($data as $item) {
            $payment = new Payment();

            if (!empty($item['assessment_item_id'])) {
                $assessmentItem = $this->getReference(
                    'assessment_item_' . $item['assessment_item_id'],
                    AssessmentItem::class
                );
                $payment->setAssessmentItem($assessmentItem);
            } else {
                $payment->setAssessmentItem(null);
            }

            if (!empty($item['contribution_schedule_id'])) {
                $contributionSchedule = $this->getReference(
                    'contribution_schedule_' . $item['contribution_schedule_id'],
                    ContributionSchedule::class
                );
                $payment->setContributionSchedule($contributionSchedule);
            } else {
                $payment->setContributionSchedule(null);
            }

            $payment->setAmount($item['amount']);
            $payment->setDate(new \DateTimeImmutable($item['date']));
            $payment->setMethod(PaymentMethod::from($item['method']));
            $payment->setReferenceNumber($item['reference_number']);
            $payment->setNotes($item['notes']);

            $recordedBy = $this->getReference('user_' . $item['recorded_by'], User::class);
            $payment->setRecorderBy($recordedBy);

            $manager->persist($payment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AssessmentItemFixtures::class,
            ContributionScheduleFixtures::class,
            UserFixtures::class
        ];
    }
}
