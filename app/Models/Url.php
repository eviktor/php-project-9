<?php

namespace App\Models;

use App\Helpers\UrlNameNormalizer;
use Carbon\Carbon;

class Url extends Model
{
    private ?string $name = null;

    public static function fromArray(array $urlData): Url
    {
        $url = new Url();
        $url->setName($urlData['name']);
        if (array_key_exists('created_at', $urlData)) {
            $url->setCreatedAt(Carbon::parse($urlData['created_at']));
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
}
