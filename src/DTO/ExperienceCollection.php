<?php

namespace App\DTO;

class ExperienceCollection
{
    public ?int $id;
    public ?string $title;
    public ?array $category;
    public ?string $description;
    public ?array $host;
    public ?string $media;
    public ?\DateTimeImmutable $createdAt;

}