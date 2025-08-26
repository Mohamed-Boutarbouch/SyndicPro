<?php

namespace App\Entity;

use App\Enum\ContributionStatus;
use App\Repository\RegularContributionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegularContributionRepository::class)]
class RegularContribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'regularContributions')]
    private ?Building $building = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $totalAnnualAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amountPerUnit = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(enumType: ContributionStatus::class)]
    private ?ContributionStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'regularContributions')]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, ContributionSchedule>
     */
    #[ORM\OneToMany(targetEntity: ContributionSchedule::class, mappedBy: 'regularContribution')]
    private Collection $contributionSchedules;

    public function __construct()
    {
        $this->contributionSchedules = new ArrayCollection();
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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getTotalAnnualAmount(): ?string
    {
        return $this->totalAnnualAmount;
    }

    public function setTotalAnnualAmount(string $totalAnnualAmount): static
    {
        $this->totalAnnualAmount = $totalAnnualAmount;

        return $this;
    }

    public function getAmountPerUnit(): ?string
    {
        return $this->amountPerUnit;
    }

    public function setAmountPerUnit(string $amountPerUnit): static
    {
        $this->amountPerUnit = $amountPerUnit;

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

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?ContributionStatus
    {
        return $this->status;
    }

    public function setStatus(ContributionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

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
            $contributionSchedule->setRegularContribution($this);
        }

        return $this;
    }

    public function removeContributionSchedule(ContributionSchedule $contributionSchedule): static
    {
        if ($this->contributionSchedules->removeElement($contributionSchedule)) {
            // set the owning side to null (unless already changed)
            if ($contributionSchedule->getRegularContribution() === $this) {
                $contributionSchedule->setRegularContribution(null);
            }
        }

        return $this;
    }
}
