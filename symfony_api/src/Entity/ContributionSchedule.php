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

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'contributionSchedule')]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
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

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setContributionSchedule($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getContributionSchedule() === $this) {
                $payment->setContributionSchedule(null);
            }
        }

        return $this;
    }
}
