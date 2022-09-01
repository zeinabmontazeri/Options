<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use App\Entity\TransactionCmdEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Transaction
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'parent_id', type: Types::INTEGER)]
    private ?int $parentId = null;
    
    #[ORM\Column(enumType: TransactionCmdEnum::class)]
    private ?TransactionCmdEnum $command = null;

    #[ORM\Column(enumType: TransactionStatusEnum::class)]
    private ?TransactionStatusEnum $status = null;

    #[ORM\Column(enumType: TransactionOriginEnum::class)]
    private ?TransactionOriginEnum $origin = null;

    #[ORM\Column(name: 'invoice_id', type: Types::INTEGER)]
    private ?int $invoiceId = null;

    #[ORM\Column(name: 'user_id', type: Types::INTEGER)]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 3)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column(name: 'callback_token', length: 255)]
    #[Assert\Unique]
    private ?string $callbackToken = null;

    #[ORM\Column(name: 'bank_status', type: Types::INTEGER)]
    private ?int $bankStatus = null;

    #[ORM\Column(name: 'bank_token', length: 255)]
    #[Assert\Unique]
    private ?string $bankToken = null;

    #[ORM\Column(name: 'card_info', length: 255)]
    private ?string $cardInfo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getCommand(): ?TransactionCmdEnum
    {
        return $this->command;
    }

    public function setCommand(TransactionCmdEnum $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getStatus(): ?TransactionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TransactionStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrigin(): ?TransactionOriginEnum
    {
        return $this->origin;
    }

    public function setOrigin(TransactionOriginEnum $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCallbackToken(): ?string
    {
        return $this->callbackToken;
    }

    public function setCallbackToken(string $callbackToken): self
    {
        $this->callbackToken = $callbackToken;

        return $this;
    }

    public function getBankStatus(): ?int
    {
        return $this->bankStatus;
    }

    public function setBankStatus(int $bankStatus): self
    {
        $this->bankStatus = $bankStatus;

        return $this;
    }

    public function getBankToken(): ?string
    {
        return $this->bankToken;
    }

    public function setBankToken(string $bankToken): self
    {
        $this->bankToken = $bankToken;

        return $this;
    }

    public function getCardInfo(): ?string
    {
        return $this->cardInfo;
    }

    public function setCardInfo(string $cardInfo): self
    {
        $this->cardInfo = $cardInfo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}