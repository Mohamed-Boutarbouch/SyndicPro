<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\ContributionFrequency;
use App\Enum\LedgerEntryPaymentMethod;

class UnitContributionResponse
{
    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public int $ownerId;

    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public int $unitId;

    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public string $unitNumber;

    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public string $ownerFirstName;

    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public string $ownerLastName;

    #[Groups(['contribution:schedule-table', 'contribution:history-table'])]
    public string $ownerFullName;

    #[Groups(['contribution:schedule-table'])]
    public string $frequency;

    #[Groups(['contribution:schedule-table'])]
    public float $amountPerPayment;

    #[Groups(['contribution:schedule-table'])]
    public ?\DateTimeImmutable $nextDueDate;

    #[Groups(['contribution:schedule-table'])]
    public string $paymentStatus;

    #[Groups(['contribution:schedule-table'])]
    public float $scheduleId;

    #[Groups(['contribution:schedule-table'])]
    public float $unitFloor;

    #[Groups(['contribution:card-stats', 'contribution:schedule-table', 'contribution:history-table'])]
    public int $buildingId;

    #[Groups(['contribution:history-table'])]
    public ?int $ledgerEntryId = null;

    #[Groups(['contribution:history-table'])]
    public ?LedgerEntryPaymentMethod $paymentMethod = null;

    #[Groups(['contribution:card-stats'])]
    public string $buildingName;

    #[Groups(['contribution:card-stats'])]
    public int $paymentYear;

    #[Groups(['contribution:card-stats'])]
    public ?\DateTimeImmutable $periodStartDate;

    #[Groups(['contribution:card-stats'])]
    public ?\DateTimeImmutable $periodEndDate;

    #[Groups(['contribution:card-stats'])]
    public float $amountPerUnit;

    #[Groups(['contribution:card-stats', 'contribution:schedule-table', 'contribution:history-table'])]
    public int $regularContributionId;

    #[Groups(['contribution:card-stats'])]
    public float $totalAnnualAmount;

    #[Groups(['contribution:card-stats'])]
    public float $totalPaidAmount;

    #[Groups(['contribution:history-table'])]
    public ?float $paidAmount = null;

    #[Groups(['contribution:history-table'])]
    public ?\DateTimeImmutable $paymentDate = null;

    #[Groups(['contribution:schedule-table'])]
    public float $actualPaidAmountPerUnit;

    #[Groups(['contribution:card-stats'])]
    public int $totalPaymentCount;

    #[Groups(['contribution:history-table'])]
    public ?string $referenceNumber = null;

    #[Groups(['contribution:history-table'])]
    public ?int $receiptId = null;

    #[Groups(['contribution:history-table'])]
    public ?string $receiptFilePath;

    public static function fromData(array $data): self
    {
        $dto = new self();

        // Basic unit info
        $dto->ownerId = (int) ($data['ownerId'] ?? 0);
        $dto->unitId = (int) ($data['unitId'] ?? 0);
        $dto->unitNumber = (string) ($data['unitNumber'] ?? '');
        $dto->ownerFirstName = (string) ($data['ownerFirstName'] ?? '');
        $dto->ownerLastName = (string) ($data['ownerLastName'] ?? '');
        $dto->ownerFullName = trim(($data['ownerLastName'] ?? '') . ' ' . ($data['ownerFirstName'] ?? ''));

        // Contribution info
        $dto->frequency = isset($data['frequency'])
            ? ($data['frequency'] instanceof ContributionFrequency ? $data['frequency']->value : (string) $data['frequency'])
            : '';
        $dto->amountPerPayment = (float) ($data['amountPerPayment'] ?? 0.0);
        $dto->nextDueDate = !empty($data['nextDueDate'])
            ? ($data['nextDueDate'] instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($data['nextDueDate'])
                : new \DateTimeImmutable((string) $data['nextDueDate']))
            : null;
        $dto->periodStartDate = !empty($data['periodStartDate'])
            ? ($data['periodStartDate'] instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($data['periodStartDate'])
                : new \DateTimeImmutable((string) $data['periodStartDate']))
            : null;
        $dto->periodEndDate = !empty($data['periodEndDate'])
            ? ($data['periodEndDate'] instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($data['periodEndDate'])
                : new \DateTimeImmutable((string) $data['periodEndDate']))
            : null;

        $dto->paymentStatus = $dto->nextDueDate !== null
            ? ((new \DateTimeImmutable()) > $dto->nextDueDate ? 'overdue' : 'paid')
            : 'paid';

        $dto->scheduleId = (int) ($data['scheduleId'] ?? 0);
        $dto->unitFloor = (int) ($data['unitFloor'] ?? 0);

        // Building info
        $dto->buildingId = (int) ($data['buildingId'] ?? 0);
        $dto->buildingName = (string) ($data['buildingName'] ?? '');
        $dto->paymentYear = (int) ($data['paymentYear'] ?? 0);

        // Regular contribution
        $dto->regularContributionId = (int) ($data['regularContributionId'] ?? 0);
        $dto->amountPerUnit = (float) ($data['amountPerUnit'] ?? 0.0);
        $dto->totalAnnualAmount = (float) ($data['totalAnnualAmount'] ?? 0.0);
        $dto->totalPaidAmount = (float) ($data['totalPaidAmount'] ?? 0.0);
        $dto->actualPaidAmountPerUnit = (float) ($data['actualPaidAmountPerUnit'] ?? 0.0);
        $dto->totalPaymentCount = (int) ($data['totalPaymentCount'] ?? 0);

        // Ledger entry info
        $dto->ledgerEntryId = isset($data['ledgerEntryId']) ? (int) $data['ledgerEntryId'] : null;
        $dto->paidAmount = isset($data['paidAmount']) ? (float) $data['paidAmount'] : null;
        $dto->paymentDate = match (true) {
            empty($data['paymentDate']) => null,
            $data['paymentDate'] instanceof \DateTimeInterface => \DateTimeImmutable::createFromInterface($data['paymentDate']),
            is_array($data['paymentDate']) && isset($data['paymentDate']['date']) => new \DateTimeImmutable($data['paymentDate']['date']),
            default => new \DateTimeImmutable((string) $data['paymentDate']),
        };
        $dto->paymentMethod = match (true) {
            empty($data['paymentMethod']) => null,
            $data['paymentMethod'] instanceof LedgerEntryPaymentMethod => $data['paymentMethod'],
            default => LedgerEntryPaymentMethod::from($data['paymentMethod']),
        };
        $dto->referenceNumber = $data['referenceNumber'] ?? null;

        $dto->receiptId = $data['receiptId'] ?? null;

        $dto->receiptFilePath = !empty($data['receiptFilePath'])
            ? 'http://localhost:8000/' . ltrim((string) $data['receiptFilePath'], '/')
            : '';

        return $dto;
    }

    public static function fromDataArray(array $dataArray): array
    {
        return array_map([self::class, 'fromData'], $dataArray);
    }
}
