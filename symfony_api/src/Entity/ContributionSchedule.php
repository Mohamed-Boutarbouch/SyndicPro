<?php

namespace App\Entity;

use App\Enum\ContributionFrequency;
use App\Repository\ContributionScheduleRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContributionScheduleRepository::class)]
class ContributionSchedule
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contributionSchedules')]
    private ?RegularContribution $regularContribution = null;

    #[ORM\ManyToOne(inversedBy: 'contributionSchedules')]
    private ?Unit $unit = null;

    #[ORM\Column(enumType: ContributionFrequency::class)]
    private ?ContributionFrequency $frequency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amountPerPayment = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $nextDueDate = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $changedAt = null;

    #[ORM\ManyToOne(inversedBy: 'contributionSchedule')]
    private ?LedgerEntry $ledgerEntry = null;

    /**
     * @var Collection<int, LedgerEntry>
     */
    #[ORM\OneToMany(targetEntity: LedgerEntry::class, mappedBy: 'contributionSchedule')]
    private Collection $ledgerEntries;

    public function __construct()
    {
        $this->ledgerEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegularContribution(): ?RegularContribution
    {
        return $this->regularContribution;
    }

    public function setRegularContribution(?RegularContribution $regularContribution): static
    {
        $this->regularContribution = $regularContribution;

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

    public function getFrequency(): ?ContributionFrequency
    {
        return $this->frequency;
    }

    public function setFrequency(ContributionFrequency $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getAmountPerPayment(): ?string
    {
        return $this->amountPerPayment;
    }

    public function setAmountPerPayment(string $amountPerPayment): static
    {
        $this->amountPerPayment = $amountPerPayment;

        return $this;
    }

    public function getNextDueDate(): ?\DateTimeImmutable
    {
        return $this->nextDueDate;
    }

    public function setNextDueDate(\DateTimeImmutable $nextDueDate): static
    {
        $this->nextDueDate = $nextDueDate;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(?\DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;

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

    /**
     * @return Collection<int, LedgerEntry>
     */
    public function getLedgerEntries(): Collection
    {
        return $this->ledgerEntries;
    }

    public function addLedgerEntry(LedgerEntry $ledgerEntry): static
    {
        if (!$this->ledgerEntries->contains($ledgerEntry)) {
            $this->ledgerEntries->add($ledgerEntry);
            $ledgerEntry->setContributionSchedule($this);
        }

        return $this;
    }

    public function removeLedgerEntry(LedgerEntry $ledgerEntry): static
    {
        if ($this->ledgerEntries->removeElement($ledgerEntry)) {
            // set the owning side to null (unless already changed)
            if ($ledgerEntry->getContributionSchedule() === $this) {
                $ledgerEntry->setContributionSchedule(null);
            }
        }

        return $this;
    }
}
