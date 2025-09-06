<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'building')]
    private Collection $users;

    /**
     * @var Collection<int, Unit>
     */
    #[ORM\OneToMany(targetEntity: Unit::class, mappedBy: 'building')]
    private Collection $units;

    /**
     * @var Collection<int, RegularContribution>
     */
    #[ORM\OneToMany(targetEntity: RegularContribution::class, mappedBy: 'building')]
    private Collection $regularContributions;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\OneToMany(targetEntity: Assessment::class, mappedBy: 'building')]
    private Collection $assessments;

    /**
     * @var Collection<int, LedgerEntry>
     */
    #[ORM\OneToMany(targetEntity: LedgerEntry::class, mappedBy: 'building')]
    private Collection $ledgerEntries;

    /**
     * @var Collection<int, Receipt>
     */
    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'building')]
    private Collection $receipts;

    /**
     * @var Collection<int, ReceiptTemplate>
     */
    #[ORM\OneToMany(targetEntity: ReceiptTemplate::class, mappedBy: 'building')]
    private Collection $receiptTemplates;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->regularContributions = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->ledgerEntries = new ArrayCollection();
        $this->receipts = new ArrayCollection();
        $this->receiptTemplates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setBuilding($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getBuilding() === $this) {
                $user->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Unit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): static
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
            $unit->setBuilding($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): static
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getBuilding() === $this) {
                $unit->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RegularContribution>
     */
    public function getRegularContributions(): Collection
    {
        return $this->regularContributions;
    }

    public function addRegularContribution(RegularContribution $regularContribution): static
    {
        if (!$this->regularContributions->contains($regularContribution)) {
            $this->regularContributions->add($regularContribution);
            $regularContribution->setBuilding($this);
        }

        return $this;
    }

    public function removeRegularContribution(RegularContribution $regularContribution): static
    {
        if ($this->regularContributions->removeElement($regularContribution)) {
            // set the owning side to null (unless already changed)
            if ($regularContribution->getBuilding() === $this) {
                $regularContribution->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Assessment>
     */
    public function getAssessments(): Collection
    {
        return $this->assessments;
    }

    public function addAssessment(Assessment $assessment): static
    {
        if (!$this->assessments->contains($assessment)) {
            $this->assessments->add($assessment);
            $assessment->setBuilding($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        if ($this->assessments->removeElement($assessment)) {
            // set the owning side to null (unless already changed)
            if ($assessment->getBuilding() === $this) {
                $assessment->setBuilding(null);
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
            $ledgerEntry->setBuilding($this);
        }

        return $this;
    }

    public function removeLedgerEntry(LedgerEntry $ledgerEntry): static
    {
        if ($this->ledgerEntries->removeElement($ledgerEntry)) {
            // set the owning side to null (unless already changed)
            if ($ledgerEntry->getBuilding() === $this) {
                $ledgerEntry->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Receipt>
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }

    public function addReceipt(Receipt $receipt): static
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts->add($receipt);
            $receipt->setBuilding($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): static
    {
        if ($this->receipts->removeElement($receipt)) {
            // set the owning side to null (unless already changed)
            if ($receipt->getBuilding() === $this) {
                $receipt->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReceiptTemplate>
     */
    public function getReceiptTemplates(): Collection
    {
        return $this->receiptTemplates;
    }

    public function addReceiptTemplate(ReceiptTemplate $receiptTemplate): static
    {
        if (!$this->receiptTemplates->contains($receiptTemplate)) {
            $this->receiptTemplates->add($receiptTemplate);
            $receiptTemplate->setBuilding($this);
        }

        return $this;
    }

    public function removeReceiptTemplate(ReceiptTemplate $receiptTemplate): static
    {
        if ($this->receiptTemplates->removeElement($receiptTemplate)) {
            // set the owning side to null (unless already changed)
            if ($receiptTemplate->getBuilding() === $this) {
                $receiptTemplate->setBuilding(null);
            }
        }

        return $this;
    }
}
