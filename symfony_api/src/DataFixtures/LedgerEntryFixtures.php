<?php

namespace App\DataFixtures;

use App\Entity\AssessmentItem;
use App\Entity\Building;
use App\Entity\ContributionSchedule;
use App\Entity\LedgerEntry;
use App\Entity\Receipt;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\LedgerEntryExpenseCategory;
use App\Enum\LedgerEntryIncomeType;
use App\Enum\LedgerEntryPaymentMethod;
use App\Enum\LedgerEntryType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LedgerEntryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/ledger_entries.json'), true);

        foreach ($data as $item) {
            $ledgerEntry = new LedgerEntry();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $ledgerEntry->setBuilding($building);

            $ledgerEntry->setType(LedgerEntryType::from($item['type']));
            $ledgerEntry->setAmount($item['amount']);
            $ledgerEntry->setDescription($item['description']);

            if (!empty($item['income_type'])) {
                $ledgerEntry->setIncomeType(LedgerEntryIncomeType::from($item['income_type']));
            } else {
                $ledgerEntry->setIncomeType(null);
            }

            if (!empty($item['unit_id'])) {
                $unit = $this->getReference(
                    'unit_' . $item['unit_id'],
                    Unit::class
                );
                $ledgerEntry->setUnit($unit);
            } else {
                $ledgerEntry->setUnit(null);
            }

            if (!empty($item['expense_category'])) {
                $ledgerEntry->setExpenseCategory(LedgerEntryExpenseCategory::from($item['expense_category']));
            } else {
                $ledgerEntry->setExpenseCategory(null);
            }

            $ledgerEntry->setVendor($item['vendor']);
            $ledgerEntry->setReferenceNumber($item['reference_number']);
            $ledgerEntry->setPaymentMethod(LedgerEntryPaymentMethod::from($item['payment_method']));

            if (!empty($item['contribution_schedule_id'])) {
                $contributionSchedule = $this->getReference(
                    'contribution_schedule_' . $item['contribution_schedule_id'],
                    ContributionSchedule::class
                );
                $ledgerEntry->setContributionSchedule($contributionSchedule);
            } else {
                $ledgerEntry->setContributionSchedule(null);
            }

            if (!empty($item['assessment_item_id'])) {
                $assessmentItem = $this->getReference(
                    'assessment_item_' . $item['assessment_item_id'],
                    AssessmentItem::class
                );
                $ledgerEntry->setAssessmentItem($assessmentItem);
            } else {
                $ledgerEntry->setAssessmentItem(null);
            }

            $recordedBy = $this->getReference('user_' . $item['recorded_by'], User::class);
            $ledgerEntry->setRecordedBy($recordedBy);

            $manager->persist($ledgerEntry);

            $this->addReference('ledger_entry_' . $item['id'], $ledgerEntry);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BuildingFixtures::class,
            AssessmentItemFixtures::class,
            ContributionScheduleFixtures::class,
            UserFixtures::class
        ];
    }
}
