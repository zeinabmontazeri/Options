<?php

namespace App\Entity;

use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Repository\HostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: HostRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Host
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'host', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\OneToMany(mappedBy: 'host', targetEntity: Experience::class)]
    private Collection $experiences;

    #[ORM\Column(name: 'approvalStatus',enumType: EnumPermissionStatus::class)]
    private EnumPermissionStatus $approvalStatus = EnumPermissionStatus::PENDING;

    #[ORM\Column(name: 'level',enumType: EnumHostBusinessClassStatus::class)]
    private EnumHostBusinessClassStatus $level = EnumHostBusinessClassStatus::NORMAL;

    public function __construct()
    {
        $this->experiences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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


    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function getFullName(): string
    {
        return $this->user->getFirstName() . ' ' . $this->user->getLastName();
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setHost($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getHost() === $this) {
                $experience->setHost(null);
            }
        }

        return $this;
    }

    public function getApprovalStatus(): EnumPermissionStatus
    {
        return $this->approvalStatus;
    }

    public function setApprovalStatus(EnumPermissionStatus $approvalStatus): self
    {
        $this->approvalStatus = $approvalStatus;

        return $this;
    }

    public function getLevel(): EnumHostBusinessClassStatus
    {
        return $this->level;
    }

    public function setLevel(EnumHostBusinessClassStatus $level): self
    {
        $this->level = $level;

        return $this;
    }
}