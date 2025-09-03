<?php

namespace App\Entity;

use App\Enum\UnitType;
use App\Repository\UnitRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnitRepository::class)]
class Unit
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'units')]
    private ?Building $building = null;

    #[ORM\Column(enumType: UnitType::class)]
    private ?UnitType $type = null;

    #[ORM\Column]
    private ?int $floor = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'units')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'unit')]
    private Collection $transactions;

    /**
     * @var Collection<int, ContributionSchedule>
     */
    #[ORM\OneToMany(targetEntity: ContributionSchedule::class, mappedBy: 'unit')]
    private Collection $contributionSchedules;

    /**
     * @var Collection<int, AssessmentItem>
     */
    #[ORM\OneToMany(targetEntity: AssessmentItem::class, mappedBy: 'unit')]
    private Collection $assessmentItems;

    /**
     * @var Collection<int, LedgerEntry>
     */
    #[ORM\OneToMany(targetEntity: LedgerEntry::class, mappedBy: 'unit')]
    private Collection $ledgerEntries;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->contributionSchedules = new ArrayCollection();
        $this->assessmentItems = new ArrayCollection();
        $this->ledgerEntries = new ArrayCollection();
    }

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

    public function getType(): ?UnitType
    {
        return $this->type;
    }

    public function setType(UnitType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): static
    {
        $this->floor = $floor;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUnit($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUnit() === $this) {
                $transaction->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContributionSchedule>
     */
    public function getContributionSchedules(): Collection
    {
        return $this->contributionSchedules;
    }

    public function addContributionSchedule(ContributionSchedule $contributionSchedule): static
    {
        if (!$this->contributionSchedules->contains($contributionSchedule)) {
            $this->contributionSchedules->add($contributionSchedule);
            $contributionSchedule->setUnit($this);
        }

        return $this;
    }

    public function removeContributionSchedule(ContributionSchedule $contributionSchedule): static
    {
        if ($this->contributionSchedules->removeElement($contributionSchedule)) {
            // set the owning side to null (unless already changed)
            if ($contributionSchedule->getUnit() === $this) {
                $contributionSchedule->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AssessmentItem>
     */
    public function getAssessmentItems(): Collection
    {
        return $this->assessmentItems;
    }

    public function addAssessmentItem(AssessmentItem $assessmentItem): static
    {
        if (!$this->assessmentItems->contains($assessmentItem)) {
            $this->assessmentItems->add($assessmentItem);
            $assessmentItem->setUnit($this);
        }

        return $this;
    }

    public function removeAssessmentItem(AssessmentItem $assessmentItem): static
    {
        if ($this->assessmentItems->removeElement($assessmentItem)) {
            // set the owning side to null (unless already changed)
            if ($assessmentItem->getUnit() === $this) {
                $assessmentItem->setUnit(null);
            }
        }

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
            $ledgerEntry->setUnit($this);
        }

        return $this;
    }

    public function removeLedgerEntry(LedgerEntry $ledgerEntry): static
    {
        if ($this->ledgerEntries->removeElement($ledgerEntry)) {
            // set the owning side to null (unless already changed)
            if ($ledgerEntry->getUnit() === $this) {
                $ledgerEntry->setUnit(null);
            }
        }

        return $this;
    }
}
