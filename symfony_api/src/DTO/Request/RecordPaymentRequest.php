<?php

namespace App\DTO\Request;

use App\Enum\LedgerEntryPaymentMethod;
use Symfony\Component\Validator\Constraints as Assert;

class RecordPaymentRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: 'Unit ID must be a positive integer')]
    public int $unitId;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive(message: 'Regular ContributionId ID must be a positive integer')]
    public int $regularContributionId;

    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    #[Assert\Positive(message: "Amount must be greater than zero.")]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    public \DateTimeInterface $paymentDate;

    #[Assert\NotNull]
    public LedgerEntryPaymentMethod $paymentMethod;

    #[Assert\Type('string')]
    public ?string $reference = null;

    #[Assert\Type('string')]
    public ?string $notes = null;

    public function __construct(
        int $unitId = 0,
        int $regularContributionId = 0,
        float $amount = 0.0,
        ?\DateTimeInterface $paymentDate = null,
        ?LedgerEntryPaymentMethod $paymentMethod = null,
        ?string $reference = null,
        ?string $notes = null
    ) {
        $this->unitId = $unitId;
        $this->regularContributionId = $regularContributionId;
        $this->amount = $amount;
        $this->paymentDate = $paymentDate ?? new \DateTimeImmutable();
        $this->paymentMethod = $paymentMethod ?? LedgerEntryPaymentMethod::CASH;
        $this->reference = $reference;
        $this->notes = $notes;
    }
}
