<?php

namespace App\Entity;

use App\Repository\ReceiptRepository;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiptRepository::class)]
class Receipt
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receipts')]
    private ?Building $building = null;

    #[ORM\ManyToOne(inversedBy: 'receipts')]
    private ?Unit $unit = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'receipts')]
    private ?ContributionSchedule $contributionSchedule = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\ManyToOne(inversedBy: 'receipts')]
    private ?User $generatedBy = null;

    #[ORM\OneToOne(inversedBy: 'receipt', cascade: ['persist', 'remove'])]
    private ?LedgerEntry $ledgerEntry = null;

    #[ORM\ManyToOne(inversedBy: 'receipts')]
    private ?ReceiptTemplate $template = null;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

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

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getGeneratedBy(): ?User
    {
        return $this->generatedBy;
    }

    public function setGeneratedBy(?User $generatedBy): static
    {
        $this->generatedBy = $generatedBy;

        return $this;
    }

    public function getLedgerEntry(): ?LedgerEntry
    {
        return $this->ledgerEntry;
    }

    public function setLedgerEntry(?LedgerEntry $ledgerEntry): static
    {
        $this->ledgerEntry = $ledgerEntry;

        return $this;
    }

    public function getTemplate(): ?ReceiptTemplate
    {
        return $this->template;
    }

    public function setTemplate(?ReceiptTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }
}
