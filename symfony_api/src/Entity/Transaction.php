<?php

namespace App\Entity;

use App\Enum\ExpenseCategory;
use App\Enum\PaymentMethod;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Building $building = null;

    #[ORM\Column(enumType: TransactionType::class)]
    private ?TransactionType $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: ExpenseCategory::class)]
    private ?ExpenseCategory $expense_category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vendor = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Unit $unit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referenceNumber = null;

    #[ORM\Column(enumType: PaymentMethod::class)]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\Column(enumType: TransactionStatus::class)]
    private ?TransactionStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?User $approvedBy = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $approvedAt = null;

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

    public function getType(): ?TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): static
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

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

    public function getExpenseCategory(): ?ExpenseCategory
    {
        return $this->expense_category;
    }

    public function setExpenseCategory(ExpenseCategory $expense_category): static
    {
        $this->expense_category = $expense_category;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

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

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getStatus(): ?TransactionStatus
    {
        return $this->status;
    }

    public function setStatus(TransactionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): static
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(\DateTimeImmutable $approvedAt): static
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }
}
