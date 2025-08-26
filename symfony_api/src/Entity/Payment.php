<?php

namespace App\Entity;

use App\Enum\PaymentMethod;
use App\Repository\PaymentRepository;
use App\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?AssessmentItem $assessmentItem = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?ContributionSchedule $contributionSchedule = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(enumType: PaymentMethod::class)]
    private ?PaymentMethod $method = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referenceNumber = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?User $recorderBy = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $recordedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContributionSchedule(): ?ContributionSchedule
    {
        return $this->contributionSchedule;
    }

    public function setContributionSchedule(?ContributionSchedule $contributionSchedule): static
    {
        $this->contributionSchedule = $contributionSchedule;

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

    public function getMethod(): ?PaymentMethod
    {
        return $this->method;
    }

    public function setMethod(PaymentMethod $method): static
    {
        $this->method = $method;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getRecorderBy(): ?User
    {
        return $this->recorderBy;
    }

    public function setRecorderBy(?User $recorderBy): static
    {
        $this->recorderBy = $recorderBy;

        return $this;
    }

    public function getRecordedAt(): ?\DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(?\DateTimeImmutable $recordedAt): static
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }
}
