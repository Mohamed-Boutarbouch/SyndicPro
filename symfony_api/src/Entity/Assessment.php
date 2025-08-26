<?php

namespace App\Entity;

use App\Enum\AssessmentDistribution;
use App\Enum\AssessmentStatus;
use App\Repository\AssessmentRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssessmentRepository::class)]
class Assessment
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'assessments')]
    private ?Building $building = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $totalAmount = null;

    #[ORM\Column(enumType: AssessmentDistribution::class)]
    private ?AssessmentDistribution $distributionMethod = null;

    #[ORM\Column(enumType: AssessmentStatus::class)]
    private ?AssessmentStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'assessments')]
    private ?User $issuedBy = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $issuedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dueDate = null;

    /**
     * @var Collection<int, AssessmentItem>
     */
    #[ORM\OneToMany(targetEntity: AssessmentItem::class, mappedBy: 'assessment')]
    private Collection $assessmentItems;

    public function __construct()
    {
        $this->assessmentItems = new ArrayCollection();
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getDistributionMethod(): ?AssessmentDistribution
    {
        return $this->distributionMethod;
    }

    public function setDistributionMethod(AssessmentDistribution $distributionMethod): static
    {
        $this->distributionMethod = $distributionMethod;

        return $this;
    }

    public function getStatus(): ?AssessmentStatus
    {
        return $this->status;
    }

    public function setStatus(AssessmentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIssuedBy(): ?User
    {
        return $this->issuedBy;
    }

    public function setIssuedBy(?User $issuedBy): static
    {
        $this->issuedBy = $issuedBy;

        return $this;
    }

    public function getIssuedAt(): ?\DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): static
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;

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
            $assessmentItem->setAssessment($this);
        }

        return $this;
    }

    public function removeAssessmentItem(AssessmentItem $assessmentItem): static
    {
        if ($this->assessmentItems->removeElement($assessmentItem)) {
            // set the owning side to null (unless already changed)
            if ($assessmentItem->getAssessment() === $this) {
                $assessmentItem->setAssessment(null);
            }
        }

        return $this;
    }
}
