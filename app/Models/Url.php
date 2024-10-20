<?php

namespace App\Models;

use App\Helpers\UrlNameNormalizer;
use Carbon\Carbon;

class Url
{
    private ?int $id = null;
    private ?string $name = null;
    private ?Carbon $createdAt = null;

    public static function fromArray(array $urlData): Url
    {
        $url = new Url();
        $url->setName($urlData['name']);
        if (array_key_exists('created_at', $urlData)) {
            $url->setCreatedAt(Carbon::parse($urlData['created_at']));
        }

        return $url;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = self::normalizeName($name);
    }

    public function setCreatedAt(?Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function exists(): bool
    {
        return !is_null($this->getId());
    }

    public static function normalizeName(string $name): string
    {
        return UrlNameNormalizer::normalize($name);
    }
}
