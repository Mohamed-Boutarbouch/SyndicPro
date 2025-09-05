<?php

namespace App\Entity;

use App\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'coOwners')]
    #[ORM\JoinColumn(name: 'syndic_id', referencedColumnName: 'id', nullable: true)]
    private ?User $syndic = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'syndic', targetEntity: User::class)]
    private Collection $coOwners;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Building $building = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, Unit>
     */
    #[ORM\OneToMany(targetEntity: Unit::class, mappedBy: 'user')]
    private Collection $units;

    /**
     * @var Collection<int, RegularContribution>
     */
    #[ORM\OneToMany(targetEntity: RegularContribution::class, mappedBy: 'createdBy')]
    private Collection $regularContributions;

    /**
     * @var Collection<int, Assessment>
     */
    #[ORM\OneToMany(targetEntity: Assessment::class, mappedBy: 'issuedBy')]
    private Collection $assessments;

    /**
     * @var Collection<int, LedgerEntry>
     */
    #[ORM\OneToMany(targetEntity: LedgerEntry::class, mappedBy: 'recordedBy')]
    private Collection $ledgerEntries;

    /**
     * @var Collection<int, Receipt>
     */
    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'createdBy')]
    private Collection $receipts;

    public function __construct()
    {
        $this->coOwners = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->regularContributions = new ArrayCollection();
        $this->assessments = new ArrayCollection();
        $this->ledgerEntries = new ArrayCollection();
        $this->receipts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSyndic(): ?self
    {
        return $this->syndic;
    }

    public function setSyndic(?self $syndic): static
    {
        $this->syndic = $syndic;

        return $this;
    }
    /**
     * @return Collection<int, self>
     */
    public function getCoOwners(): Collection
    {
        return $this->coOwners;
    }

    public function addCoOwner(self $coOwner): static
    {
        if (!$this->coOwners->contains($coOwner)) {
            $this->coOwners->add($coOwner);
            $coOwner->setSyndic($this);
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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
            $unit->setUser($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): static
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getUser() === $this) {
                $unit->setUser(null);
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
            $regularContribution->setCreatedBy($this);
        }

        return $this;
    }

    public function removeRegularContribution(RegularContribution $regularContribution): static
    {
        if ($this->regularContributions->removeElement($regularContribution)) {
            // set the owning side to null (unless already changed)
            if ($regularContribution->getCreatedBy() === $this) {
                $regularContribution->setCreatedBy(null);
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
            $assessment->setIssuedBy($this);
        }

        return $this;
    }

    public function removeAssessment(Assessment $assessment): static
    {
        if ($this->assessments->removeElement($assessment)) {
            // set the owning side to null (unless already changed)
            if ($assessment->getIssuedBy() === $this) {
                $assessment->setIssuedBy(null);
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
            $ledgerEntry->setRecordedBy($this);
        }

        return $this;
    }

    public function removeLedgerEntry(LedgerEntry $ledgerEntry): static
    {
        if ($this->ledgerEntries->removeElement($ledgerEntry)) {
            // set the owning side to null (unless already changed)
            if ($ledgerEntry->getRecordedBy() === $this) {
                $ledgerEntry->setRecordedBy(null);
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
            $receipt->setCreatedBy($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): static
    {
        if ($this->receipts->removeElement($receipt)) {
            // set the owning side to null (unless already changed)
            if ($receipt->getCreatedBy() === $this) {
                $receipt->setCreatedBy(null);
            }
        }

        return $this;
    }
}
