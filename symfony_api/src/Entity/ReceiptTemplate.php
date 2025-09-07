<?php

namespace App\Entity;

use App\Repository\ReceiptTemplateRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiptTemplateRepository::class)]
class ReceiptTemplate
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receiptTemplates')]
    private ?Building $building = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'json')]
    private array $placeholders = [];

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\ManyToOne(inversedBy: 'receiptTemplates')]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, Receipt>
     */
    #[ORM\OneToMany(targetEntity: Receipt::class, mappedBy: 'template')]
    private Collection $receipts;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    public function setPlaceholders(array $placeholders): static
    {
        $this->placeholders = $placeholders;

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
            $receipt->setTemplate($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): static
    {
        if ($this->receipts->removeElement($receipt)) {
            // set the owning side to null (unless already changed)
            if ($receipt->getTemplate() === $this) {
                $receipt->setTemplate(null);
            }
        }

        return $this;
    }
}
