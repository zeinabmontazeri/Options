<?php

namespace App\DTO;


class EventCollection
{
    public ?int $id = null;
    public ?float $price = null;
    public ?int $capacity = null;
    public ?int $duration = null;
    public ?bool $isOnline = null;
    public ?\DateTime $startsAt = null;
    public ?string $link = null;
    public ?string $address = null;

}