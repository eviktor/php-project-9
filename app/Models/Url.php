<?php

namespace App\Models;

use App\Helpers\UrlNameNormalizer;
use Carbon\Carbon;

class Url extends Model
{
    private ?string $name = null;
    private ?Carbon $lastCheckedAt = null;
    private ?int $lastStatusCode = null;

    public static function fromArray(array $urlData): Url
    {
        $url = new Url();
        $url->setName($urlData['name']);
        if (array_key_exists('created_at', $urlData)) {
            $url->setCreatedAt($urlData['created_at']);
        }
        if (array_key_exists('last_checked_at', $urlData)) {
            $url->setLastCheckedAt($urlData['last_checked_at']);
        }
        if (array_key_exists('last_status_code', $urlData)) {
            $url->setLastStatusCode($urlData['last_status_code']);
        }

        return $url;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = self::normalizeName($name);
    }

    public static function normalizeName(string $name): string
    {
        return UrlNameNormalizer::normalize($name);
    }

    public function getLastCheckedAt(): ?Carbon
    {
        return $this->lastCheckedAt;
    }

    public function setLastCheckedAt(?string $lastCheckedAt): void
    {
        $this->lastCheckedAt = empty($lastCheckedAt) ? null : Carbon::parse($lastCheckedAt);
    }

    public function getLastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }

    public function setLastStatusCode(?int $lastStatusCode): void
    {
        $this->lastStatusCode = $lastStatusCode;
    }
}
