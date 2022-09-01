<?php

namespace App\DTO;

class ExperienceFilterCollection
{
    public ?int $id;
    public ?string $title;
    public ?array $category;
    public ?string $description;
    public ?array $host;
    public ?string $media;
    public ?\DateTimeImmutable $createdAt;
}
