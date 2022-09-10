<?php

namespace App\Entity;

use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Repository\ExperienceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[ORM\Cache(usage: 'READ_ONLY')]
class Experience
{
    use SoftDeleteableEntity;

    // TODO: Decide about cascading soft deleting on host_id and category_id
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['experience'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['experience'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Unique]
    #[Groups(['experience'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['experience'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'experiences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Host $host = null;

    #[ORM\ManyToOne(inversedBy: 'experiences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'experience', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\Column(name: 'status', enumType: EnumEventStatus::class)]
    private EnumEventStatus $status = EnumEventStatus::DRAFT;

    #[ORM\Column(name: 'approvalStatus', enumType: EnumPermissionStatus::class)]
    private EnumPermissionStatus $approvalStatus = EnumPermissionStatus::PENDING;

    #[ORM\OneToMany(mappedBy: 'experience', targetEntity: Media::class)]
    private Collection $media;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getHost(): ?Host
    {
        return $this->host;
    }

    public function setHost(?Host $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setExperience($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getExperience() === $this) {
                $event->setExperience(null);
            }
        }

        return $this;
    }

    public function getStatus(): EnumEventStatus
    {
        return $this->status;
    }

    public function setStatus(EnumEventStatus $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setExperience($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getExperience() === $this) {
                $medium->setExperience(null);
            }
        }

        return $this;
    }

    public function getMediaFileNames()
    {
        $res = [];
        foreach ($this->getMedia() as $media) {
            $res[] = $media->getFileName();
        }

        return $res;
    }
}