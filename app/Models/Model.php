<?php

namespace App\Models;

use Carbon\Carbon;

abstract class Model
{
    protected ?int $id = null;
    private ?Carbon $createdAt = null;

    abstract public static function fromArray(array $urlData): self;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCreatedAt(?Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function exists(): bool
    {
        return !is_null($this->getId());
    }
}
