<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Transaction;
use App\Entity\Unit;
use App\Entity\User;
use App\Enum\ExpenseCategory;
use App\Enum\PaymentMethod;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/transactions.json'), true);

        foreach ($data as $item) {
            $transaction = new Transaction();

            $building = $this->getReference('building_' . $item['building_id'], Building::class);
            $transaction->setBuilding($building);

            $transaction->setType(TransactionType::from($item['type']));
            $transaction->setAmount($item['amount']);
            $transaction->setDate(new \DateTimeImmutable($item['date']));
            $transaction->setDescription($item['description']);

            if (!empty($item['expense_category'])) {
                $transaction->setExpenseCategory(ExpenseCategory::from($item['expense_category']));
            } else {
                $transaction->setExpenseCategory(null);
            }

            $transaction->setVendor($item['vendor']);

            if (!empty($item['unit_id'])) {
                $unit = $this->getReference('unit_' . $item['unit_id'], Unit::class);
                $transaction->setUnit($unit);
            } else {
                $transaction->setUnit(null);
            }

            $transaction->setReferenceNumber($item['reference_number']);
            $transaction->setPaymentMethod(PaymentMethod::from($item['payment_method']));
            $transaction->setStatus(TransactionStatus::from($item['status']));

            if (!empty($item['user_id'])) {
                $approvedBy = $this->getReference('approved_by' . $item['user_id'], User::class);
                $transaction->setApprovedBy($approvedBy);
            } else {
                $transaction->setApprovedBy(null);
            }

            $transaction->setApprovedAt(new \DateTimeImmutable($item['approved_at']));

            // $this->addReference('transaction_' . $item['id'], $transaction);

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BuildingFixtures::class,
            UnitFixtures::class,
            UserFixtures::class
        ];
    }
}
