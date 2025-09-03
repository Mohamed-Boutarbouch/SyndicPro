<?php

namespace App\Entity;

use App\Enum\LedgerEntryExpenseCategory;
use App\Enum\LedgerEntryIncomeType;
use App\Enum\LedgerEntryPaymentMethod;
use App\Repository\LedgerEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LedgerEntryRepository::class)]
class LedgerEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ledgerEntries')]
    private ?Building $building = null;

    #[ORM\Column(enumType: LedgerEntryIncomeType::class)]
    private ?LedgerEntryIncomeType $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: LedgerEntryIncomeType::class)]
    private ?LedgerEntryIncomeType $incomeType = null;

    #[ORM\ManyToOne(inversedBy: 'ledgerEntries')]
    private ?Unit $unit = null;

    #[ORM\Column(enumType: LedgerEntryExpenseCategory::class)]
    private ?LedgerEntryExpenseCategory $expenseCategory = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vendor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referenceNumber = null;

    #[ORM\Column(enumType: LedgerEntryPaymentMethod::class)]
    private ?LedgerEntryPaymentMethod $paymentMethod = null;

    #[ORM\ManyToOne(inversedBy: 'ledgerEntries')]
    private ?ContributionSchedule $contributionSchedule = null;

    #[ORM\ManyToOne(inversedBy: 'ledgerEntries')]
    private ?AssessmentItem $assessmentItem = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getType(): ?LedgerEntryIncomeType
    {
        return $this->type;
    }

    public function setType(LedgerEntryIncomeType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIncomeType(): ?LedgerEntryIncomeType
    {
        return $this->incomeType;
    }

    public function setIncomeType(LedgerEntryIncomeType $incomeType): static
    {
        $this->incomeType = $incomeType;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getExpenseCategory(): ?LedgerEntryExpenseCategory
    {
        return $this->expenseCategory;
    }

    public function setExpenseCategory(LedgerEntryExpenseCategory $expenseCategory): static
    {
        $this->expenseCategory = $expenseCategory;

        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): static
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(?string $referenceNumber): static
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getPaymentMethod(): ?LedgerEntryPaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(LedgerEntryPaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getContributionSchedule(): ?ContributionSchedule
    {
        return $this->contributionSchedule;
    }

    public function setContributionSchedule(?ContributionSchedule $contributionSchedule): static
    {
        $this->contributionSchedule = $contributionSchedule;

        return $this;
    }

    public function getAssessmentItem(): ?AssessmentItem
    {
        return $this->assessmentItem;
    }

    public function setAssessmentItem(?AssessmentItem $assessmentItem): static
    {
        $this->assessmentItem = $assessmentItem;

        return $this;
    }
}
