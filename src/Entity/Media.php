<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    private ?Experience $experience = null;

    #[ORM\Column(length: 255)]
    #[Groups(['experiencer', 'host'])]
    private ?string $fileName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function uploadMedia(UploadedFile $file)
    {
        $fileFormat = $file->getClientOriginalExtension();
        $fileName = $file->getClientOriginalName();
        $fileNameSave = base64_encode($fileName) . '_' . base64_encode($this->getExperience()->getId()) . '_' . uniqid() . '.' . $fileFormat;
        $file->move('./media', $fileNameSave);
        return $fileNameSave;
    }
}
